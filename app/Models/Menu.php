<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Menu extends Model
{
 protected $fillable=['name','location'];
 public function allItems(){return $this->hasMany(MenuItem::class);}
 public function items(){return $this->hasMany(MenuItem::class)->whereNull('parent_id')->where('is_active',true)->orderBy('sort_order')->with(['children'=>fn($q)=>$q->where('is_active',true)->orderBy('sort_order')]);}
 public static function cached(string $location):?self{return cache()->remember('menu:'.$location,3600,fn()=>static::where('location',$location)->with('items.children.children.children.children')->first());}
}