<?php

namespace App\Http\Requests\Admin\Post;

use App\Models\Post;
use App\Rules\UniqueLocalizedSlug;
use App\Support\CanonicalUrlNormalizer;
use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
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
                new UniqueLocalizedSlug(Post::class),
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
            'taxonomies' => 'nullable|array',
            'taxonomies.*' => 'exists:taxonomies,id',
            'excerpt' => 'nullable|array',
            'excerpt.*' => 'nullable|string',
            'featured_image' => 'nullable|array',
            'featured_image.*' => 'nullable|string',
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
