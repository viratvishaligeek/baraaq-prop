<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class HomepageSection extends Model
{
    protected $fillable = [
        'homepage_id',
        'section_key',
        'section_type',
        'sort_order',
        'is_enabled',
        'config',
        'data_source',
    ];

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
            'config' => 'array',
            'data_source' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (HomepageSection $section): void {
            $section->mediaAttachments()->delete();
        });
    }

    public function homepage(): BelongsTo
    {
        return $this->belongsTo(Homepage::class);
    }

    public function mediaAttachments(): MorphMany
    {
        return $this->morphMany(MediaAttachment::class, 'mediable')->with('media');
    }
}
