<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\SoftDeletes;
class TeamMember extends Model { use SoftDeletes; protected $fillable=['name','designation','bio','photo','email','phone','social_links','sort_order','is_active']; protected function casts():array{return ['social_links'=>'array','is_active'=>'boolean'];} }