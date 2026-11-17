<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WidgetAreaItem extends Model
{
    protected $fillable = [
        'widget_area_id',
        'widget_id',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function area()
    {
        return $this->belongsTo(WidgetArea::class, 'widget_area_id');
    }

    public function widget()
    {
        return $this->belongsTo(Widget::class);
    }
}
