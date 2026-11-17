<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WidgetArea extends Model
{
    protected $fillable = [
        'name',
        'location',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function items()
    {
        return $this->hasMany(WidgetAreaItem::class)->orderBy('sort_order');
    }

    public function widgets()
    {
        return $this->belongsToMany(Widget::class, 'widget_area_items')
            ->withPivot(['sort_order', 'is_active'])
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }
}
