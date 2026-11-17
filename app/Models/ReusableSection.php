<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReusableSection extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'section_type',
        'section_key',
        'source_design',
        'config',
        'status',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'config' => 'array',
        ];
    }

    public function mediaAttachments()\n    {\n        return $this->morphMany(MediaAttachment::class, 'mediable')->with('media');\n    }\n\n    public function homepageSections()
    {
        return $this->hasMany(HomepageSection::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
