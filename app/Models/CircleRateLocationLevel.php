<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CircleRateLocationLevel extends Model
{
    protected $table = 'circle_rate_location_levels';
    protected $fillable = ['name', 'slug', 'description', 'is_required', 'is_active', 'sort_order'];
    protected function casts(): array { return ['is_required' => 'boolean', 'is_active' => 'boolean']; }
}
