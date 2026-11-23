<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class GalleryItem extends Model {
 use SoftDeletes;
 protected $fillable=['title','slug','description','category','image','alt_text','sort_order','status','published_at','meta_title','meta_description','canonical_url','og_image'];
 protected function casts(): array{return ['published_at'=>'datetime'];}
 public function scopePublished($q){return $q->where('status','published')->whereNotNull('published_at')->where('published_at','<=',now());}
}