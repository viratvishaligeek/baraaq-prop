<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CircleRate extends Model
{
    use SoftDeletes;

    protected $table = 'circle_rates';

    protected $fillable = [
        'district_id', 'tehsil_id', 'location_id', 'area_type_id',
        'property_type_id', 'category_id', 'road_width_range_id',
        'financial_year_id', 'unit_id', 'source_id', 'rate', 'priority',
        'effective_from', 'effective_until', 'verification_status',
        'status', 'conditions', 'admin_notes', 'created_by',
        'verified_by', 'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'rate' => 'decimal:4',
            'conditions' => 'array',
            'effective_from' => 'date',
            'effective_until' => 'date',
            'verified_at' => 'datetime',
        ];
    }

    public function district() { return $this->belongsTo(CircleRateDistrict::class, 'district_id'); }
    public function tehsil() { return $this->belongsTo(CircleRateTehsil::class, 'tehsil_id'); }
    public function location() { return $this->belongsTo(CircleRateLocation::class, 'location_id'); }
    public function areaType() { return $this->belongsTo(CircleRateAreaType::class, 'area_type_id'); }
    public function propertyType() { return $this->belongsTo(CircleRatePropertyType::class, 'property_type_id'); }
    public function category() { return $this->belongsTo(CircleRatePropertyCategory::class, 'category_id'); }
    public function roadWidthRange() { return $this->belongsTo(CircleRateRoadWidthRange::class, 'road_width_range_id'); }
    public function financialYear() { return $this->belongsTo(CircleRateFinancialYear::class, 'financial_year_id'); }
    public function unit() { return $this->belongsTo(CircleRateUnit::class, 'unit_id'); }
    public function source() { return $this->belongsTo(CircleRateSource::class, 'source_id'); }
    public function versions() { return $this->hasMany(CircleRateVersion::class, 'circle_rate_id')->orderByDesc('version_number'); }

    public function scopePublishedAndVerified($query)
    {
        return $query->where('status', 'published')->where('verification_status', 'verified');
    }
}
