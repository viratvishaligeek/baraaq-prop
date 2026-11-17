<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Widget extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'config',
        'status',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'config' => 'array',
        ];
    }

    public function areaItems()
    {
        return $this->hasMany(WidgetAreaItem::class);
    }

    public function areas()
    {
        return $this->belongsToMany(WidgetArea::class, 'widget_area_items')
            ->withPivot(['sort_order', 'is_active'])
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }
}
