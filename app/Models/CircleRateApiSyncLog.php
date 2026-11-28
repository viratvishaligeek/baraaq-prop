<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CircleRateApiSyncLog extends Model
{
    protected $table = 'circle_rate_api_sync_logs';
    protected $fillable = ['connection_id', 'status', 'endpoint', 'http_status', 'request_summary', 'response_summary', 'error_message', 'started_at', 'finished_at'];
    protected function casts(): array { return ['request_summary' => 'array', 'response_summary' => 'array', 'started_at' => 'datetime', 'finished_at' => 'datetime']; }
    public function connection() { return $this->belongsTo(CircleRateApiConnection::class, 'connection_id'); }
}
