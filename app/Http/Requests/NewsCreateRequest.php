<?php

namespace App\Http\Requests;

use App\Repositories\NewsRepository;
use App\Rules\SchemaStringMaxRule;
use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class NewsCreateRequest extends FormRequest
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
    #[ArrayShape(['title' => "array", 'content' => "array"])]
    public function rules(): array
    {
        $newsRepository = app(NewsRepository::class);
        return [
            'title' => ['required', 'string', new SchemaStringMaxRule($newsRepository)],
            'content' => ['required', 'string', new SchemaStringMaxRule($newsRepository)]
        ];
    }
}
