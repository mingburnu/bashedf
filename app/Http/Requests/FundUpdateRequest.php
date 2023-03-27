<?php

namespace App\Http\Requests;

use App\Entities\Fund;
use App\Rules\DecimalEqRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use JetBrains\PhpStorm\ArrayShape;

class FundUpdateRequest extends FormRequest
{
    protected ?Fund $fund;

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
    #[ArrayShape(['merchant_id' => "array", 'amount' => "array"])]
    public function rules(): array
    {
        $this->fund = $this->route()->parameter('fund');
        return [
            'merchant_id' => ['required', 'string', Rule::in([$this->fund->user->merchant_id])],
            'amount' => ['required', 'numeric', 'confirmed', new DecimalEqRule($this->fund->amount)],
        ];
    }
}