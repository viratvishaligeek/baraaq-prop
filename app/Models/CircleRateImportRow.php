<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CircleRateImportRow extends Model
{
    protected $table = 'circle_rate_import_rows';
    protected $fillable = ['batch_id', 'row_number', 'original_data', 'normalized_data', 'validation_errors', 'status', 'circle_rate_id'];
    protected function casts(): array { return ['original_data' => 'array', 'normalized_data' => 'array', 'validation_errors' => 'array']; }
    public function batch() { return $this->belongsTo(CircleRateImportBatch::class, 'batch_id'); }
    public function rate() { return $this->belongsTo(CircleRate::class, 'circle_rate_id'); }
}
