<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CircleRateTehsil extends Model
{
    use SoftDeletes;

    protected $table = 'circle_rate_tehsils';
    protected $fillable = ['district_id', 'name', 'slug', 'code', 'is_active'];
    protected function casts(): array { return ['is_active' => 'boolean']; }
    public function district() { return $this->belongsTo(CircleRateDistrict::class, 'district_id'); }
    public function locations() { return $this->hasMany(CircleRateLocation::class, 'tehsil_id'); }
}
