<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Testimonial extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'designation',
        'company',
        'quote',
        'photo',
        'rating',
        'sort_order',
        'is_active',
        'status',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
