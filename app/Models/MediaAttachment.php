<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaAttachment extends Model
{
    protected $fillable = ['media_id', 'field_key'];

    public function mediable()
    {
        return $this->morphTo();
    }

    public function media()
    {
        return $this->belongsTo(Media::class);
    }
}
