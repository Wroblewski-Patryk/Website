<?php

namespace App\Services\AdminSearch\Providers;

use App\Models\Post;

class PostSearchProvider extends AbstractLocalizedContentSearchProvider
{
    public function key(): string
    {
        return 'posts';
    }

    protected function modelClass(): string
    {
        return Post::class;
    }

    protected function resultType(): string
    {
        return 'post';
    }

    protected function editRouteName(): string
    {
        return 'admin.posts.edit';
    }
}
