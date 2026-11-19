<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes;
    protected $fillable = ['author_id', 'title', 'slug', 'excerpt', 'content', 'featured_image', 'status', 'published_at', 'meta_title', 'meta_description', 'canonical_url', 'og_image', 'focus_keyword'];
    protected function casts(): array
    {
        return ['published_at' => 'datetime'];
    }
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
    public function categories()
    {
        return $this->morphToMany(Category::class, 'categorizable');
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function scopePublished($q)
    {
        return $q->where('status', 'published')->whereNotNull('published_at')->where('published_at', '<=', now());
    }
}
