<?php

namespace App\Http\Requests;

use App\Entities\User;
use App\Repositories\FundRepository;
use App\Rules\DecimalGtRule;
use App\Rules\DecimalMaxRule;
use App\Rules\SchemaScaleMaxRule;
use App\Rules\SchemaStringMaxRule;
use Brick\Math\BigDecimal;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use JetBrains\PhpStorm\ArrayShape;

class FundCreateRequest extends FormRequest
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
        $fundRepository = app(FundRepository::class);
        $this->user = $this->route()->parameter('user');
        $max = BigDecimal::of($this->user->wallet->balance)->jsonSerialize();
        return [
            'merchant_id' => ['required', 'string', Rule::in([$this->user->merchant_id])],
            'amount' => ['required', 'numeric', 'confirmed', new DecimalGtRule('0'), new DecimalMaxRule($max), new SchemaScaleMaxRule($fundRepository)],
            'cause' => ['nullable', 'string', new SchemaStringMaxRule($fundRepository)]
        ];
    }
}