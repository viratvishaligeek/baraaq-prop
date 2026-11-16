<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\SoftDeletes;
class Page extends Model { use SoftDeletes; protected $fillable=['author_id','title','slug','content','template','status','meta_title','meta_description']; }