<?php

namespace App\Rules;

use App\Services\SignService;
use Illuminate\Contracts\Validation\Rule;

class SignRule implements Rule
{
    protected array $data;
    protected string $api_key;

    /**
     * Create a new rule instance.
     *
     * @param array $data
     * @param string $api_key
     */
    public function __construct(array $data, string $api_key)
    {
        $this->data = $data;
        $this->api_key = $api_key;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        return $value === app(SignService::class)->sign(collect($this->data)->except([$attribute])->toArray(), $this->api_key)['sign'];
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.in', []);
    }
}
