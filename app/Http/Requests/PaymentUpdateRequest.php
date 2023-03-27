<?php

namespace App\Http\Requests;

use App\Entities\Payment;
use Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentUpdateRequest extends FormRequest
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
        return match ($this->route()->getName()) {
            'admin.payments.update' => $this->payment->admin_id === Auth::guard('admin')->user()->id,
            'admin.payments.task.lock' => auth('admin')->check(),
            default => false,
        };
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $name = $this->route()->getName();
        return match ($name) {
            'admin.payments.update' => [
                'status' => ['required', Rule::in([1, -1])],
            ],
            default => [],
        };
    }
}
