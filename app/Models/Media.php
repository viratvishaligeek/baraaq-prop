<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Media extends Model { protected $table='media'; protected $fillable=['uploaded_by','disk','path','original_name','mime_type','size','alt_text']; public function uploader(){return $this->belongsTo(User::class,'uploaded_by');} }