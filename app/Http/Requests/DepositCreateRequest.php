<?php

namespace App\Http\Requests;

use App\Repositories\PaymentRepository;
use App\Repositories\ContractRepository;
use App\Rules\DecimalBetweenRule;
use App\Rules\SchemaScaleMaxRule;
use Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use JetBrains\PhpStorm\ArrayShape;

class DepositCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Auth::guard('user')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    #[ArrayShape(['amount' => "array", 'bank_card_id' => "array"])]
    public function rules(): array
    {
        $merchant = Auth::user();
        $contract = app(ContractRepository::class)->findOrFail($merchant->id);
        return [
            'amount' => ['required', new DecimalBetweenRule($contract->min_deposit_amount, $contract->max_deposit_amount), new SchemaScaleMaxRule(app(PaymentRepository::class))],
            'bank_card_id' => ['required', 'integer', Rule::in($merchant->bankCards()->pluck('id'))],
        ];
    }
}