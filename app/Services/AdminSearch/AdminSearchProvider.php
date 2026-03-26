<?php

namespace App\Services\AdminSearch;

interface AdminSearchProvider
{
    public function key(): string;

    /**
     * @return array<int, AdminSearchResult>
     */
    public function search(string $query, int $limit = 5): array;
}
