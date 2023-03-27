<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return auth('user')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $name = $this->route()->getName();
        $this->route('profile.password.reset');
        return match ($name) {
            'profile.api-token-switch.configure' => [
                'password' => ['required', 'password:user'],
                'api_token_switch' => ['required', Rule::in([1, 0])]
            ],
            'profile.default-payment-callback-url.link' => [
                'default_payment_callback_url' => ['nullable', 'string', 'between:1,255']
            ],
            'profile.white-list.fill' => [
                'password' => ['required', 'password:user'],
                'white_ips' => ['nullable', 'array', 'max:50'],
                'white_ips.*' => ['nullable', 'string', 'ip'],
            ],
            default => [],
        };
    }
}