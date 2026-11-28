<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CircleRateImportBatch extends Model
{
    protected $table = 'circle_rate_import_batches';
    protected $fillable = ['financial_year_id', 'source_id', 'uploaded_by', 'original_filename', 'stored_path', 'file_hash', 'file_type', 'status', 'column_mapping', 'total_rows', 'valid_rows', 'invalid_rows', 'conflict_rows', 'summary', 'confirmed_at'];
    protected function casts(): array { return ['column_mapping' => 'array', 'confirmed_at' => 'datetime']; }
    public function rows() { return $this->hasMany(CircleRateImportRow::class, 'batch_id'); }
    public function financialYear() { return $this->belongsTo(CircleRateFinancialYear::class, 'financial_year_id'); }
    public function source() { return $this->belongsTo(CircleRateSource::class, 'source_id'); }
}
