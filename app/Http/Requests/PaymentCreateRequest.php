<?php

namespace App\Http\Requests;

use App\Entities\User;
use App\Repositories\PaymentRepository;
use App\Rules\DecimalBetweenRule;
use App\Rules\DecimalGteRule;
use App\Rules\Google2FARule;
use App\Rules\SchemaScaleMaxRule;
use App\Rules\SchemaStringMaxRule;
use App\Rules\SignRule;
use App\Services\NumberService;
use App\Services\SignService;
use Brick\Math\BigDecimal;
use Curl;
use GuzzleHttp\Psr7\MimeType;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PaymentCreateRequest extends FormRequest
{
    protected ?User $clerk;
    protected ?User $merchant;
    protected array $raw_inputs = [];

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        $headers = ['Accept' => MimeType::fromExtension('json')];
        if (auth('sanctum')->check()) {
            $this->merchant = auth('sanctum')->user();
            $url = $this->merchant->authorizer->api ?? null;
            $this->raw_inputs = $this->except(['sign']);

            if ($this->merchant->authorizer->supervising) {
                if (Str::length($url) !== 0) {
                    $parameters = $this->merchant->authorizer->additional_parameters ?? collect();
                    $data = $parameters->merge([
                        'merchant_id' => $this->merchant->merchant_id,
                        'customized_id' => $this->get('customized_id'),
                        'account_number' => $this->get('account_number'),
                        'amount' => $this->get('amount'),
                        'currency' => 'rmb'
                    ])->toArray();
                    $response = Curl::to($url)->withData(app(SignService::class)->sign($data, $this->merchant->api_key))->withHeaders($headers)->post();

                    if ($response !== false) {
                        $result = json_decode($response, true);
                        if ($result[$this->merchant->authorizer->boolean_index] ?? false === true) {
                            $this->merge(['balance' => $this->merchant->wallet->balance]);
                            return true;
                        }
                    }
                }
                return false;
            } else {
                $this->merge(['balance' => $this->merchant->wallet->balance]);
                return true;
            }
        } else if (auth('user')->check()) {
            $this->clerk = auth('user')->user();
            $this->merchant = is_null($this->clerk->api_key) ? $this->clerk->node->parent->user : $this->clerk;
            $this->merge(['balance' => $this->merchant->wallet->balance]);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $repository = app(PaymentRepository::class);
        if (auth('sanctum')->check()) {
            $processing_fee = $this->merchant->contract->payment_processing_fee;
            $amount = $this->input('amount');
            return [
                'sign' => ['required', 'string', new SignRule($this->raw_inputs, $this->merchant->api_key)],
                'account_name' => ['required', 'string', new SchemaStringMaxRule($repository)],
                'account_number' => ['required', 'string', 'regex:/^[\d]{6,30}$/'],
                'bank_name' => ['required', 'string', new SchemaStringMaxRule($repository)],
                'customized_id' => ['required', 'string', 'between:1,36', Rule::unique($repository->getTable(), 'customized_id')
                    ->where(function (Builder $builder) {
                        return $builder->where('user_id', auth('sanctum')->id());
                    })],
                'callback_url' => ['nullable', 'string', 'between:1,255'],
                'amount' => ['required', 'numeric', new DecimalBetweenRule($this->merchant->contract->min_payment_amount, $this->merchant->contract->max_payment_amount), new SchemaScaleMaxRule($repository)],
                'branch' => ['required', 'string', new SchemaStringMaxRule($repository)],
                'balance' => ['required', function ($attribute, $value, $fail) use ($amount, $processing_fee) {
                    if (app(NumberService::class)->isNumeric($amount)) {
                        $total_amount = BigDecimal::of($amount)->plus($processing_fee);
                        if (BigDecimal::of($value)->isLessThan($total_amount)) {
                            $fail(__('validation.gte.numeric', ['value' => $total_amount]));
                        }
                    }
                }]
            ];
        } else if (auth('user')->check()) {
            $orders = $this->input('payments');
            $table = $repository->getTable();
            $total_amounts = [];
            if (is_array($orders)) {
                $processing_fee = $this->merchant->contract->payment_processing_fee;
                $rows = collect($orders);
                $rows->each(function ($row) use (&$total_amounts, $processing_fee) {
                    $amount = $row['amount'] ?? null;
                    if (app(NumberService::class)->isNumeric($amount)) {
                        $total_amounts[] = BigDecimal::of($amount)->plus($processing_fee);
                    }
                });

                $total_amounts = empty($total_amounts) ? ['0'] : $total_amounts;
            }

            return [
                'payments' => ['required', 'array', 'min:1'],
                'google_key' => ['required', 'string', new Google2FARule($this->merchant->google2fa_secret)],
                'balance' => ['required', new DecimalGteRule(BigDecimal::sum(...$total_amounts))],
                'payments.*.account_name' => ['required', 'string', new SchemaStringMaxRule($repository)],
                'payments.*.amount' => ['required', 'numeric', new DecimalBetweenRule($this->merchant->contract->min_payment_amount, $this->merchant->contract->max_payment_amount), new SchemaScaleMaxRule($repository)],
                'payments.*.bank_name' => ['required', 'string', new SchemaStringMaxRule($repository)],
                'payments.*.account_number' => ['required', 'string', 'regex:/^[\d]{6,30}$/'],
                'payments.*.customized_id' => ['required', 'string', 'between:1,36', 'distinct', Rule::unique($table, 'customized_id')
                    ->where(function (Builder $builder) {
                        return $builder->where('user_id', $this->merchant->id);
                    })],
                'payments.*.callback_url' => ['nullable', 'string', 'between:1,255'],
                'payments.*.branch' => ['required', 'string', new SchemaStringMaxRule($repository)],
            ];
        } else {
            return [];
        }
    }

    protected function failedAuthorization()
    {
        if (auth('sanctum')->check()) {
            throw new HttpResponseException(response()->json(['message' => 'This action is unauthorized. Illegal request.'], 403));
        } else {
            parent::failedAuthorization();
        }
    }
}