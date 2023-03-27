<?php

namespace App\Http\Requests;

use App\Repositories\BankCardRepository;
use App\Rules\SchemaStringMaxRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use JetBrains\PhpStorm\ArrayShape;

class BankCardUpdateRequest extends FormRequest
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
    #[ArrayShape(['account_name' => "array", 'bank_name' => "array", 'account_number' => "array", 'bank_district' => "array", 'bank_address' => "array"])]
    public function rules(): array
    {
        $id = $this->route()->originalParameter('bank_card');
        $bankCardRepository = app(BankCardRepository::class);
        return [
            'account_name' => ['required', 'string', new SchemaStringMaxRule($bankCardRepository)],
            'bank_name' => ['required', 'string', new SchemaStringMaxRule($bankCardRepository)],
            'account_number' => ['required', 'string', 'regex:/^[\d]{6,30}$/', Rule::unique($bankCardRepository->getTable(), 'account_number')->ignore($id)],
            'bank_district' => ['required', 'string', new SchemaStringMaxRule($bankCardRepository)],
            'bank_address' => ['required', 'string', new SchemaStringMaxRule($bankCardRepository)]
        ];
    }
}
