<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\SoftDeletes;
class Category extends Model { use SoftDeletes; protected $fillable=['name','slug','description','icon_path','sort_order','is_active','parent_id','type']; public function parent(){return $this->belongsTo(self::class,'parent_id');} public function children(){return $this->hasMany(self::class,'parent_id');} public function posts(){return $this->morphedByMany(Post::class,'categorizable');} public function properties(){return $this->hasMany(Property::class);} }