<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CircleRateRule extends Model
{
    protected $table = 'circle_rate_rules';
    protected $fillable = ['name', 'description', 'conditions', 'actions', 'priority', 'stop_processing', 'is_active', 'effective_from', 'effective_until', 'created_by'];
    protected function casts(): array { return ['conditions' => 'array', 'actions' => 'array', 'priority' => 'integer', 'stop_processing' => 'boolean', 'is_active' => 'boolean', 'effective_from' => 'date', 'effective_until' => 'date']; }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}
