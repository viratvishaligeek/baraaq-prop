<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CircleRateSource extends Model
{
    protected $table = 'circle_rate_sources';
    protected $fillable = ['title', 'reference_number', 'document_path', 'document_disk', 'source_url', 'published_on', 'notes', 'uploaded_by', 'is_demo'];
    protected function casts(): array { return ['published_on' => 'date', 'is_demo' => 'boolean']; }
    public function uploader() { return $this->belongsTo(User::class, 'uploaded_by'); }
    public function rates() { return $this->hasMany(CircleRate::class, 'source_id'); }
    public function rules() { return $this->hasMany(CircleRateRule::class, 'source_id'); }
}
