<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceComment extends Model
{
    protected $fillable = ['service_id', 'name', 'email', 'body', 'status'];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
