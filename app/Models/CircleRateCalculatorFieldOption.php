<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CircleRateCalculatorFieldOption extends Model
{
    protected $table = 'circle_rate_calculator_field_options';
    protected $fillable = ['field_id', 'label', 'value', 'sort_order', 'is_active'];
    protected function casts(): array { return ['is_active' => 'boolean']; }
    public function field() { return $this->belongsTo(CircleRateCalculatorField::class, 'field_id'); }
}
