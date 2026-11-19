<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyAccommodation extends Model
{
    protected $fillable = ['property_id', 'name', 'category', 'area', 'area_unit', 'description', 'is_included', 'attributes', 'sort_order'];

    protected function casts(): array
    {
        return ['area' => 'decimal:2', 'is_included' => 'boolean', 'attributes' => 'array'];
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
