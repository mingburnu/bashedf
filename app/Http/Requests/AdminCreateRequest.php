<?php

namespace App\Http\Requests;

use App\Repositories\AdminRepository;
use App\Repositories\PermissionRepository;
use App\Repositories\UserRepository;
use App\Rules\SchemaStringMaxRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use JetBrains\PhpStorm\ArrayShape;

class AdminCreateRequest extends FormRequest
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
    #[ArrayShape(['name' => "array", 'email' => "array", 'password' => "string[]", 'users' => "string[]", 'users.*' => "array", 'permissions' => "string[]", 'permissions.*' => "array"])]
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', new SchemaStringMaxRule(app(AdminRepository::class))],
            'email' => ['required', 'string', 'email', new SchemaStringMaxRule(app(AdminRepository::class)), Rule::unique(app(AdminRepository::class)->getTable())],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'users' => ['nullable', 'array'],
            'users.*' => ['integer', Rule::in(app(UserRepository::class)->whereNotNull('api_key')->pluck('id'))],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', Rule::in(app(PermissionRepository::class)->where('id', '>', 2)->pluck('id'))]
        ];
    }
}
