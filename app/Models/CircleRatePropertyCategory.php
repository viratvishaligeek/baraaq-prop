<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CircleRatePropertyCategory extends Model
{
    use SoftDeletes;

    protected $table = 'circle_rate_property_categories';
    protected $fillable = ['name', 'slug', 'description', 'priority', 'applicability', 'is_active'];
    protected function casts(): array { return ['applicability' => 'array', 'is_active' => 'boolean']; }
    public function propertyTypes() { return $this->belongsToMany(CircleRatePropertyType::class, 'circle_rate_category_property_type', 'category_id', 'property_type_id'); }
    public function locations() { return $this->belongsToMany(CircleRateLocation::class, 'circle_rate_category_location', 'category_id', 'location_id'); }
}
