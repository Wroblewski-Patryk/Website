<?php

namespace App\Http\Requests\Admin\Template;

use App\Support\CanonicalUrlNormalizer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreTemplateRequest extends FormRequest
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
            'type' => 'required|in:header,footer,sidebar,page',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('canonical_url')) {
            $this->merge([
                'canonical_url' => CanonicalUrlNormalizer::normalize($this->input('canonical_url')),
            ]);
        }
    }
}
