<?php

namespace App\Http\Requests;

use App\Repositories\BankCardRepository;
use App\Repositories\ContractRepository;
use App\Repositories\UserRepository;
use App\Rules\DecimalBetweenRule;
use App\Rules\SchemaScaleMaxRule;
use App\Rules\SchemaStringMaxRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserCreateRequest extends FormRequest
{
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
    public function rules(): array
    {
        $userRepository = app(UserRepository::class);
        $contractRepository = app(ContractRepository::class);
        return [
            'name' => ['required', 'string', new SchemaStringMaxRule($userRepository)],
            'email' => ['required', 'string', 'email', new SchemaStringMaxRule($userRepository), 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'company' => ['required', 'string', new SchemaStringMaxRule($userRepository)],
            'phone' => ['required', 'string', new SchemaStringMaxRule($userRepository)],
            'payment_processing_fee' => ['required', 'numeric', new DecimalBetweenRule('0', '100000'), new SchemaScaleMaxRule($contractRepository)],
            'deposit_processing_fee_percent' => ['required', 'numeric', new DecimalBetweenRule('0', '100'), new SchemaScaleMaxRule($contractRepository)],
            'min_deposit_amount' => ['required', 'integer', 'gte:1'],
            'max_deposit_amount' => ['required', 'integer', 'gt:min_deposit_amount', 'max:100000000'],
            'min_payment_amount' => ['required', 'integer', 'gte:1'],
            'max_payment_amount' => ['required', 'integer', 'gt:min_payment_amount', 'max:100000000'],
            'bank_cards' => ['required', 'array'],
            'bank_cards.*' => ['integer', Rule::in(app(BankCardRepository::class)->get(['id'])->modelKeys())],
        ];
    }
}
