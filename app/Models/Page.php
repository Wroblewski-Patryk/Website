<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $guarded = [];

    protected $casts = [
        'content' => 'array',
        'settings' => 'array',
    ];
    public function headerOverride()
    {
        return $this->belongsTo(Template::class , 'header_override_id');
    }

    public function footerOverride()
    {
        return $this->belongsTo(Template::class , 'footer_override_id');
    }
}
