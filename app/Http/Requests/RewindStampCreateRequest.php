<?php

namespace App\Http\Requests;

use App\Entities\Payment;
use App\Rules\DecimalGteRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use JetBrains\PhpStorm\ArrayShape;

class RewindStampCreateRequest extends FormRequest
{
    protected ?Payment $payment;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        $this->payment = $this->route()->parameter('payment');
        $this->merge(['balance' => $this->payment->user->wallet->balance]);
        return auth('admin')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    #[ArrayShape(['merchant_id' => "array", 'account_name' => "array", 'account_number' => "array", 'balance' => "\App\Rules\DecimalGteRule[]"])]
    public function rules(): array
    {
        return [
            'merchant_id' => ['required', Rule::in($this->payment->user->merchant_id)],
            'account_name' => ['required', Rule::in($this->payment->account_name)],
            'account_number' => ['required', Rule::in($this->payment->account_number)],
            'balance' => [new DecimalGteRule($this->payment->total_amount)]
        ];
    }
}