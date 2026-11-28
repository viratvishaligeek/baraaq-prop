<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CircleRateApiConnection extends Model
{
    protected $table = 'circle_rate_api_connections';
    protected $fillable = ['name', 'base_url', 'auth_type', 'encrypted_credentials', 'configuration', 'is_enabled', 'last_synced_at'];
    protected function casts(): array { return ['configuration' => 'array', 'is_enabled' => 'boolean', 'last_synced_at' => 'datetime']; }
    public function syncLogs() { return $this->hasMany(CircleRateApiSyncLog::class, 'connection_id'); }
}
