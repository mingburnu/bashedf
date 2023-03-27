<?php

namespace App\Http\Requests;

use App\Entities\User;
use App\Repositories\UserRepository;
use App\Rules\SchemaStringMaxRule;
use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class ChildUpdateRequest extends FormRequest
{
    protected ?User $child;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        $this->child = $this->route()->parameter('child');
        return $this->child->node->parent->user->id === auth('user')->id();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    #[ArrayShape(['name' => "array", 'password' => "string[]"])]
    public function rules(): array
    {
        $repository = app(UserRepository::class);
        return [
            'name' => ['required', 'string', new SchemaStringMaxRule($repository)],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ];
    }
}