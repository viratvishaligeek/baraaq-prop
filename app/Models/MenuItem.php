<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class MenuItem extends Model { protected $fillable=['menu_id','parent_id','label','url','target','sort_order','is_active']; protected function casts():array{return ['is_active'=>'boolean'];} public function menu(){return $this->belongsTo(Menu::class);} public function children(){return $this->hasMany(self::class,'parent_id')->orderBy('sort_order');} }