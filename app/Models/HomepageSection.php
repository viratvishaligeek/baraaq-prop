<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageSection extends Model
{
    protected $fillable = [
        'homepage_id',
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

    public function homepage()
    {
        return $this->belongsTo(Homepage::class);
    }

    public function mediaAttachments()\n    {\n        return $this->morphMany(MediaAttachment::class, 'mediable')->with('media');\n    }\n\n    public function reusableSection()
    {
        return $this->belongsTo(ReusableSection::class);
    }
}
