<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ServiceImage extends Model {
 protected $fillable=['service_id','path','alt_text','sort_order'];
 public function service(){return $this->belongsTo(Service::class);}
}