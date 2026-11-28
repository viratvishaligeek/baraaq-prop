<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CircleRateLocation extends Model
{
    use SoftDeletes;

    protected $table = 'circle_rate_locations';
    protected $fillable = ['district_id', 'tehsil_id', 'area_type_id', 'level_id', 'parent_id', 'name', 'slug', 'code', 'attributes', 'is_active'];
    protected function casts(): array { return ['attributes' => 'array', 'is_active' => 'boolean']; }
    public function district() { return $this->belongsTo(CircleRateDistrict::class, 'district_id'); }
    public function tehsil() { return $this->belongsTo(CircleRateTehsil::class, 'tehsil_id'); }
    public function areaType() { return $this->belongsTo(CircleRateAreaType::class, 'area_type_id'); }
    public function parent() { return $this->belongsTo(self::class, 'parent_id'); }
    public function children() { return $this->hasMany(self::class, 'parent_id'); }
}
