<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'status',
        'published_at',
        'meta_title',
        'meta_description',
        'canonical_url',
        'og_image',
        'focus_keyword',
    ];

    protected function casts(): array
    {
        return ['published_at' => 'datetime'];
    }

    public function homepageSections()
    {
        return $this->hasMany(HomepageSection::class)->orderBy('sort_order');
    }

    public function features()
    {
        return $this->hasMany(ServiceFeature::class)->orderBy('sort_order');
    }

    public function images()
    {
        return $this->hasMany(ServiceImage::class)->orderBy('sort_order');
    }

    public function comments()
    {
        return $this->hasMany(ServiceComment::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }
}
