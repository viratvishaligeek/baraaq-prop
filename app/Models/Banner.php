<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Banner extends Model { protected $fillable=['title','subtitle','description','image','button_text','button_url','placement','sort_order','is_active','starts_at','ends_at']; protected function casts():array{return ['is_active'=>'boolean','starts_at'=>'datetime','ends_at'=>'datetime'];} public function scopeVisible($q){return $q->where('is_active',true)->where(fn($x)=>$x->whereNull('starts_at')->orWhere('starts_at','<=',now()))->where(fn($x)=>$x->whereNull('ends_at')->orWhere('ends_at','>=',now()));} }