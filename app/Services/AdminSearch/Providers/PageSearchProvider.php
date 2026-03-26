<?php

namespace App\Services\AdminSearch\Providers;

use App\Models\Page;
use Illuminate\Database\Eloquent\Model;

class PageSearchProvider extends AbstractLocalizedContentSearchProvider
{
    public function key(): string
    {
        return 'pages';
    }

    protected function modelClass(): string
    {
        return Page::class;
    }

    protected function resultType(): string
    {
        return 'page';
    }

    protected function editRouteName(): string
    {
        return 'admin.pages.edit';
    }
}
