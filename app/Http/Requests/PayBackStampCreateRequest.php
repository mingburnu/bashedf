<?php

namespace App\Http\Requests;

use App\Entities\Payment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use JetBrains\PhpStorm\ArrayShape;

class PayBackStampCreateRequest extends FormRequest
{
    protected ?Payment $payment;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    #[ArrayShape(['merchant_id' => "array", 'account_name' => "array", 'account_number' => "array"])]
    public function rules(): array
    {
        $this->payment = $this->route()->parameter('payment');
        return [
            'merchant_id' => ['required', Rule::in($this->payment->user->merchant_id)],
            'account_name' => ['required', Rule::in($this->payment->account_name)],
            'account_number' => ['required', Rule::in($this->payment->account_number)],
        ];
    }
}