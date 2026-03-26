<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminSearch\AdminSearchManager;
use Illuminate\Http\Request;

class AdminSearchController extends Controller
{
    public function __invoke(Request $request, AdminSearchManager $searchManager)
    {
        $validated = $request->validate([
            'query' => ['required', 'string', 'min:2', 'max:120'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:20'],
        ]);

        $query = trim((string) $validated['query']);
        $limit = (int) ($validated['limit'] ?? 5);
        $results = $searchManager->search($query, $limit);

        return $this->jsonSuccess([
            'results' => $results,
            'meta' => [
                'query' => $query,
                'total' => count($results),
                'limit' => $limit,
            ],
        ]);
    }
}
