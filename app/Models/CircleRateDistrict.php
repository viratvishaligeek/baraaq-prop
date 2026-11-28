<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CircleRateDistrict extends Model
{
    use SoftDeletes;

    protected $table = 'circle_rate_districts';
    protected $fillable = ['name', 'slug', 'code', 'state', 'is_active'];
    protected function casts(): array { return ['is_active' => 'boolean']; }
    public function tehsils() { return $this->hasMany(CircleRateTehsil::class, 'district_id'); }
    public function locations() { return $this->hasMany(CircleRateLocation::class, 'district_id'); }
}
