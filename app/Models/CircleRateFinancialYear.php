<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CircleRateFinancialYear extends Model
{
    protected $table = 'circle_rate_financial_years';
    protected $fillable = ['name', 'starts_on', 'ends_on', 'is_current', 'is_active', 'show_on_frontend'];
    protected function casts(): array { return ['starts_on' => 'date', 'ends_on' => 'date', 'is_current' => 'boolean', 'is_active' => 'boolean', 'show_on_frontend' => 'boolean']; }
    public function rates() { return $this->hasMany(CircleRate::class, 'financial_year_id'); }
}
