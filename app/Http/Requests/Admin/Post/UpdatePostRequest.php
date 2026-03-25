<?php

namespace App\Http\Requests\Admin\Post;

use App\Models\Post;
use App\Rules\NotReservedRouteSlug;
use App\Rules\UniqueLocalizedSlug;
use App\Support\CanonicalUrlNormalizer;
use App\Support\LocalizedSlugNormalizer;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $post = $this->route('post');
        $ignoreId = $post?->id;

        return [
            'title' => 'required|array',
            'title.*' => 'nullable|string',
            'slug' => 'required|array',
            'slug.*' => [
                'nullable',
                'string',
                new NotReservedRouteSlug(),
                new UniqueLocalizedSlug(Post::class, $ignoreId),
            ],
            'content' => 'required|array',
            'status' => 'nullable|string',
            'optimistic_lock' => 'nullable|date',
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
        $payload = [];

        if ($this->has('slug')) {
            $payload['slug'] = LocalizedSlugNormalizer::normalizeTranslations($this->input('slug'));
        }

        if ($this->has('canonical_url')) {
            $payload['canonical_url'] = CanonicalUrlNormalizer::normalize($this->input('canonical_url'));
        }

        if (!empty($payload)) {
            $this->merge($payload);
        }
    }
}
