<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class NeighborhoodGuide extends Model {
 use SoftDeletes;
 protected $fillable=['title','slug','locality','city','state','excerpt','content','hero_image','status','published_at','meta_title','meta_description','canonical_url','og_image','focus_keyword'];
 protected function casts(): array{return ['published_at'=>'datetime'];}
 public function scopePublished($q){return $q->where('status','published')->whereNotNull('published_at')->where('published_at','<=',now());}
}