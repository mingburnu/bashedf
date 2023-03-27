<?php

namespace App\Http\Requests;

use App\Repositories\AuthorizerRepository;
use App\Rules\SchemaStringMaxRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AuthorizerUpdateRequest extends FormRequest
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
        return match ($name) {
            'authorizer.update' => [
                'api' => ['required', 'string', 'url', new SchemaStringMaxRule(app(AuthorizerRepository::class))],
                'boolean_index' => ['required', 'string', new SchemaStringMaxRule(app(AuthorizerRepository::class))],
                'additional_parameters' => ['nullable', 'json', new SchemaStringMaxRule(app(AuthorizerRepository::class))]
            ],
            'authorizer.configure' => [
                'password' => ['required', 'password:user'],
                'supervising' => ['required', Rule::in([1, 0])]
            ],
            default => [],
        };
    }
}