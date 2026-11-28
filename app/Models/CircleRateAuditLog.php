<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CircleRateAuditLog extends Model
{
    protected $table = 'circle_rate_audit_logs';
    protected $fillable = ['user_id', 'subject_type', 'subject_id', 'event', 'old_values', 'new_values', 'reason', 'source_id', 'ip_address'];
    protected function casts(): array { return ['old_values' => 'array', 'new_values' => 'array']; }
    public function user() { return $this->belongsTo(User::class); }
    public function source() { return $this->belongsTo(CircleRateSource::class, 'source_id'); }
}
