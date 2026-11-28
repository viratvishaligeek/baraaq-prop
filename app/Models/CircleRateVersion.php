<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CircleRateVersion extends Model
{
    protected $table = 'circle_rate_versions';
    protected $fillable = ['circle_rate_id', 'version_number', 'snapshot', 'change_reason', 'changed_by'];
    protected function casts(): array { return ['snapshot' => 'array']; }
    public function rate() { return $this->belongsTo(CircleRate::class, 'circle_rate_id'); }
    public function changer() { return $this->belongsTo(User::class, 'changed_by'); }
}
