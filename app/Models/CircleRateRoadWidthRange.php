<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CircleRateRoadWidthRange extends Model
{
    protected $table = 'circle_rate_road_width_ranges';
    protected $fillable = ['name', 'unit_id', 'minimum_width', 'maximum_width', 'exact_width', 'applicability', 'adjustment_configuration', 'effective_from', 'effective_until', 'priority', 'is_active'];
    protected function casts(): array { return ['minimum_width' => 'decimal:4', 'maximum_width' => 'decimal:4', 'exact_width' => 'decimal:4', 'applicability' => 'array', 'adjustment_configuration' => 'array', 'effective_from' => 'date', 'effective_until' => 'date', 'is_active' => 'boolean']; }
}
