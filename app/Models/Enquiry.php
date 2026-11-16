<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Enquiry extends Model { protected $fillable=['property_id','name','subject','email','phone','message','source','status','admin_notes','contacted_at']; protected function casts():array{return ['contacted_at'=>'datetime'];} public function property(){return $this->belongsTo(Property::class);} }