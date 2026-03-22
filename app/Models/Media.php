<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $guarded = [];

    protected $appends = ['url'];

    public function getUrlAttribute()
    {
        // If path is absolute (starts with http or /), return as is
        if (str_starts_with($this->path, 'http') || str_starts_with($this->path, '/')) {
            return $this->path;
        }

        return asset('storage/' . $this->path);
    }

    public function folder()
    {
        return $this->belongsTo(MediaFolder::class , 'folder_id');
    }
}
