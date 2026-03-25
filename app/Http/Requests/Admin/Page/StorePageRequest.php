<?php

namespace App\Http\Requests\Admin\Page;

use App\Models\Page;
use App\Support\CanonicalUrlNormalizer;
use Illuminate\Foundation\Http\FormRequest;

class StorePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|array',
            'title.*' => 'nullable|string',
            'slug' => 'required|array',
            'slug.*' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    if (!$value) {
                        return;
                    }
                    $locale = str_replace('slug.', '', $attribute);
                    $exists = Page::where("slug->{$locale}", $value)->exists();
                    if ($exists) {
                        $fail("The slug for locale {$locale} is already taken.");
                    }
                },
            ],
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
            'header_override_id' => 'nullable|exists:templates,id',
            'footer_override_id' => 'nullable|exists:templates,id',
            'sidebar_override_id' => 'nullable|exists:templates,id',
            'template_id' => 'nullable|exists:templates,id',
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
