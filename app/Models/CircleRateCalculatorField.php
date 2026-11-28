<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CircleRateCalculatorField extends Model
{
    protected $table = 'circle_rate_calculator_fields';
    protected $fillable = ['name', 'label', 'input_type', 'data_source', 'placeholder', 'help_text', 'validation_rules', 'visibility_conditions', 'applicability', 'default_value', 'is_required', 'is_active', 'sort_order'];
    protected function casts(): array { return ['validation_rules' => 'array', 'visibility_conditions' => 'array', 'applicability' => 'array', 'is_required' => 'boolean', 'is_active' => 'boolean']; }
    public function options() { return $this->hasMany(CircleRateCalculatorFieldOption::class, 'field_id')->where('is_active', true)->orderBy('sort_order'); }
}
