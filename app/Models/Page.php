<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'author_id',
        'title',
        'slug',
        'content',
        'template',
        'status',
        'homepage_key',
        'meta_title',
        'meta_description',
        'canonical_url',
        'og_image',
    ];

    protected static function booted(): void
    {
        static::saving(function (Page $page): void {
            if ($page->status !== 'published') {
                $page->homepage_key = null;
            }
        });

        static::deleting(function (Page $page): void {
            if ($page->homepage_key !== null) {
                $page->forceFill(['homepage_key' => null])->saveQuietly();
            }
        });
    }
}
