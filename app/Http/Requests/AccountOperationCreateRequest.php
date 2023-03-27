<?php

namespace App\Http\Requests;

use App\Entities\User;
use App\Repositories\AccountOperationRepository;
use App\Rules\DecimalBetweenRule;
use App\Rules\DecimalNonZeroRule;
use App\Rules\SchemaScaleMaxRule;
use App\Rules\SchemaStringMaxRule;
use Brick\Math\BigDecimal;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use JetBrains\PhpStorm\ArrayShape;

class AccountOperationCreateRequest extends FormRequest
{
    protected ?User $user;

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
    #[ArrayShape(['merchant_id' => "array", 'amount' => "array", 'cause' => "array"])]
    public function rules(): array
    {
        $this->user = $this->route()->parameter('user');
        $min = BigDecimal::of($this->user->wallet->balance)->negated()->jsonSerialize();
        $accountOperationRepository = app(AccountOperationRepository::class);
        return [
            'merchant_id' => ['required', 'string', Rule::in([$this->user->merchant_id])],
            'amount' => ['required', 'numeric', 'confirmed', new DecimalNonZeroRule(), new DecimalBetweenRule($min, '500000'), new SchemaScaleMaxRule($accountOperationRepository)],
            'cause' => ['nullable', 'string', new SchemaStringMaxRule($accountOperationRepository)]
        ];
    }
}
