<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CircleRatePropertyType extends Model
{
    use SoftDeletes;

    protected $table = 'circle_rate_property_types';
    protected $fillable = ['name', 'slug', 'description', 'calculation_strategy', 'field_configuration', 'formula_configuration', 'is_active', 'sort_order'];
    protected function casts(): array { return ['field_configuration' => 'array', 'formula_configuration' => 'array', 'is_active' => 'boolean']; }
    public function categories() { return $this->belongsToMany(CircleRatePropertyCategory::class, 'circle_rate_category_property_type', 'property_type_id', 'category_id'); }
}
