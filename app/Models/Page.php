<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Page extends Model
{
    use SoftDeletes;
    protected $fillable = ['author_id', 'title', 'slug', 'content', 'template', 'status', 'meta_title', 'meta_description', 'canonical_url', 'og_image', 'focus_keyword', 'homepage_key'];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function sections()
    {
        return $this->hasMany(HomepageSection::class)->orderBy('sort_order');
    }
}
