<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CircleRateUnit extends Model
{
    protected $table = 'circle_rate_units';
    protected $fillable = ['name', 'symbol', 'slug', 'dimension', 'base_factor', 'is_base_unit', 'is_active'];
    protected function casts(): array { return ['base_factor' => 'decimal:12', 'is_base_unit' => 'boolean', 'is_active' => 'boolean']; }
}
