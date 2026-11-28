<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CircleRateAreaType extends Model
{
    protected $table = 'circle_rate_area_types';
    protected $fillable = ['name', 'slug', 'description', 'is_active', 'sort_order'];
    protected function casts(): array { return ['is_active' => 'boolean']; }
}
