<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    protected $table = 'media';

    protected $fillable = [
        'uploaded_by',
        'disk',
        'path',
        'small_path',
        'medium_path',
        'large_path',
        'original_name',
        'mime_type',
        'size',
        'alt_text',
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function attachments()
    {
        return $this->hasMany(MediaAttachment::class);
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    public function getThumbnailUrlAttribute(): string
    {
        return Storage::disk($this->disk)->url($this->small_path ?: $this->path);
    }

    public function getMediumUrlAttribute(): string
    {
        return Storage::disk($this->disk)->url($this->medium_path ?: $this->path);
    }

    public function getLargeUrlAttribute(): string
    {
        return Storage::disk($this->disk)->url($this->large_path ?: $this->path);
    }
}
