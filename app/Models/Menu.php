<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Menu extends Model {
 protected $fillable=['name','location'];
 public function items(){return $this->hasMany(MenuItem::class)->whereNull('parent_id')->orderBy('sort_order')->with('children');}
 public static function cached(string $location):?self{return cache()->remember('menu:'.$location,3600,fn()=>static::where('location',$location)->with('items')->first());}
}