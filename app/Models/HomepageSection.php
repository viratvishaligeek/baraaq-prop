<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class HomepageSection extends Model
{
    protected $fillable = [
        'homepage_id',
        'page_id',
        'service_id',
        'reusable_section_id',
        'instance_key',
        'section_key',
        'section_type',
        'source_design',
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
        static::creating(function (HomepageSection $section): void {
            if (blank($section->instance_key)) {
                $section->instance_key = (string) Str::uuid();
            }
        });

        static::deleting(function (HomepageSection $section): void {
            $section->mediaAttachments()->delete();
        });
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function reusableSection(): BelongsTo
    {
        return $this->belongsTo(ReusableSection::class);
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
