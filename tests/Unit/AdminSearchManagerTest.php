<?php

namespace Tests\Unit;

use App\Services\AdminSearch\AdminSearchManager;
use App\Services\AdminSearch\AdminSearchProvider;
use App\Services\AdminSearch\AdminSearchResult;
use PHPUnit\Framework\TestCase;

class AdminSearchManagerTest extends TestCase
{
    public function test_manager_aggregates_results_and_sorts_by_score_descending(): void
    {
        $providerA = new class implements AdminSearchProvider
        {
            public function key(): string
            {
                return 'pages';
            }

            public function search(string $query, int $limit = 5): array
            {
                return [
                    new AdminSearchResult('page', 11, 'Page A', '/pages/11', null, 0.4),
                ];
            }
        };

        $providerB = new class implements AdminSearchProvider
        {
            public function key(): string
            {
                return 'posts';
            }

            public function search(string $query, int $limit = 5): array
            {
                return [
                    new AdminSearchResult('post', 22, 'Post B', '/posts/22', null, 0.9),
                ];
            }
        };

        $manager = new AdminSearchManager([$providerA, $providerB]);
        $results = $manager->search('test');

        $this->assertCount(2, $results);
        $this->assertSame('post', $results[0]['type']);
        $this->assertSame(22, $results[0]['id']);
        $this->assertSame('page', $results[1]['type']);
        $this->assertSame(11, $results[1]['id']);
    }
}
