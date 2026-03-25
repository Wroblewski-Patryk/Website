<?php

namespace App\Http\Requests\Admin\Page;

use App\Models\Page;
use App\Rules\NotReservedRouteSlug;
use App\Rules\UniqueLocalizedSlug;
use App\Support\CanonicalUrlNormalizer;
use App\Support\LocalizedSlugNormalizer;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $page = $this->route('page');
        $ignoreId = $page?->id;

        return [
            'title' => 'required|array',
            'title.*' => 'nullable|string',
            'slug' => 'required|array',
            'slug.*' => [
                'nullable',
                'string',
                new NotReservedRouteSlug(),
                new UniqueLocalizedSlug(Page::class, $ignoreId),
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
            'header_override_id' => 'nullable|exists:templates,id',
            'footer_override_id' => 'nullable|exists:templates,id',
            'sidebar_override_id' => 'nullable|exists:templates,id',
            'template_id' => 'nullable|exists:templates,id',
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
