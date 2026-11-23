<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class FaqItem extends Model {
 use SoftDeletes;
 protected $fillable=['question','answer','category','sort_order','is_active'];
}