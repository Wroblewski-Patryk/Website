<?php

namespace App\Http\Requests\Admin\Form;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('manage-settings');
    }

    public function rules(): array
    {
        return [
            'title' => 'required|array',
            'title.*' => 'nullable|string',
            'content' => 'required|array',
            'status' => 'nullable|string',
            'published_at' => 'nullable|date',
            'meta_title' => 'nullable|array',
            'meta_title.*' => 'nullable|string',
            'meta_description' => 'nullable|array',
            'meta_description.*' => 'nullable|string',
            'canonical_url' => 'nullable|string|max:2048|url:http,https',
            'og_image' => 'nullable|array',
            'og_image.*' => 'nullable|string',
            'seo_index' => 'nullable|boolean',
            'seo_follow' => 'nullable|boolean',
            'settings' => 'nullable|array',
        ];
    }
}
