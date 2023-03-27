<?php

namespace App\Http\Requests;

use App\Repositories\UserRepository;
use App\Rules\SchemaStringMaxRule;
use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class ChildCreateRequest extends FormRequest
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
    #[ArrayShape(['name' => "array", 'email' => "array", 'password' => "string[]"])]
    public function rules(): array
    {
        $repository = app(UserRepository::class);
        return [
            'name' => ['required', 'string', new SchemaStringMaxRule($repository)],
            'email' => ['required', 'string', 'email', new SchemaStringMaxRule($repository), 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ];
    }
}