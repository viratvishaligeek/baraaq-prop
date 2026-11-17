<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Homepage extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'design_key',
        'status',
        'is_default',
        'is_composition_complete',
        'meta_title',
        'meta_description',
        'published_at',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'is_composition_complete' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function sections()
    {
        return $this->hasMany(HomepageSection::class)->orderBy('sort_order');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeDefault(Builder $query): Builder
    {
        return $query->where('is_default', true);
    }
}
