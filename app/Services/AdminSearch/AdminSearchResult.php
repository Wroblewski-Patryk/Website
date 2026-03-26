<?php

namespace App\Services\AdminSearch;

class AdminSearchResult
{
    public function __construct(
        public readonly string $type,
        public readonly int|string $id,
        public readonly string $title,
        public readonly string $url,
        public readonly ?string $subtitle = null,
        public readonly float $score = 0.0,
    ) {
    }

    /**
     * @return array<string, int|string|float|null>
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'id' => $this->id,
            'title' => $this->title,
            'url' => $this->url,
            'subtitle' => $this->subtitle,
            'score' => $this->score,
        ];
    }
}
