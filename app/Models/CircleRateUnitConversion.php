<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CircleRateUnitConversion extends Model
{
    protected $table = 'circle_rate_unit_conversions';
    protected $fillable = ['from_unit_id', 'to_unit_id', 'factor', 'source_note', 'is_verified'];
    protected function casts(): array { return ['factor' => 'decimal:12', 'is_verified' => 'boolean']; }
    public function fromUnit() { return $this->belongsTo(CircleRateUnit::class, 'from_unit_id'); }
    public function toUnit() { return $this->belongsTo(CircleRateUnit::class, 'to_unit_id'); }
}
