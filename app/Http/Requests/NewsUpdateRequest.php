<?php

namespace App\Http\Requests;

use App\Repositories\NewsRepository;
use App\Rules\SchemaStringMaxRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NewsUpdateRequest extends FormRequest
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
        $name = $this->route()->getName();
        $repository = app(NewsRepository::class);
        return match ($name) {
            'admin.news.status.change' => [
                'status' => ['required', Rule::in(['1', '0'])],
            ],
            'admin.news.update' => [
                'title' => ['required', 'string', new SchemaStringMaxRule($repository)],
                'content' => ['required', 'string', new SchemaStringMaxRule($repository)]
            ],
            default => [],
        };
    }
}