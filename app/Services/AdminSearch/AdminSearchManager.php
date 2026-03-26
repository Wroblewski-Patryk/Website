<?php

namespace App\Services\AdminSearch;

class AdminSearchManager
{
    /**
     * @param  iterable<AdminSearchProvider>  $providers
     */
    public function __construct(private readonly iterable $providers)
    {
    }

    /**
     * @return array<int, array<string, int|string|float|null>>
     */
    public function search(string $query, int $perProviderLimit = 5): array
    {
        $results = [];

        foreach ($this->providers as $provider) {
            foreach ($provider->search($query, $perProviderLimit) as $result) {
                $results[] = $result->toArray();
            }
        }

        usort($results, static function (array $a, array $b): int {
            return ($b['score'] <=> $a['score']);
        });

        return $results;
    }
}
