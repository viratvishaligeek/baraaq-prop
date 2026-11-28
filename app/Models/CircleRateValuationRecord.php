<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CircleRateValuationRecord extends Model
{
    protected $table = 'circle_rate_valuation_records';
    protected $fillable = ['reference', 'circle_rate_id', 'previous_circle_rate_id', 'financial_year_id', 'input_data', 'calculation_breakdown', 'property_area', 'area_unit_id', 'applicable_rate', 'estimated_value', 'previous_year_value', 'difference_amount', 'difference_percent', 'result_status', 'valued_at'];
    protected function casts(): array { return ['input_data' => 'array', 'calculation_breakdown' => 'array', 'property_area' => 'decimal:6', 'applicable_rate' => 'decimal:4', 'estimated_value' => 'decimal:2', 'previous_year_value' => 'decimal:2', 'difference_amount' => 'decimal:2', 'difference_percent' => 'decimal:4', 'valued_at' => 'datetime']; }
    public function rate() { return $this->belongsTo(CircleRate::class, 'circle_rate_id'); }
    public function financialYear() { return $this->belongsTo(CircleRateFinancialYear::class, 'financial_year_id'); }
}
