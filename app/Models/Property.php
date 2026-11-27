<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'created_by',
        'title',
        'slug',
        'reference',
        'property_type',
        'listing_type',
        'price',
        'price_label',
        'bedrooms',
        'bathrooms',
        'area',
        'area_unit',
        'address',
        'city',
        'state',
        'postal_code',
        'latitude',
        'longitude',
        'map_url',
        'description',
        'meta_title',
        'meta_description',
        'canonical_url',
        'og_image',
        'featured_image',
        'status',
        'is_featured',
        'cta_mode',
        'whatsapp_number',
        'whatsapp_message',
        'published_at',
        'features',
        'country',
        'availability_status',
        'focus_keyword',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'area' => 'decimal:2',
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
            'features' => 'array',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function images()
    {
        return $this->hasMany(PropertyImage::class)->orderBy('sort_order');
    }

    public function accommodations()
    {
        return $this->hasMany(PropertyAccommodation::class)->orderBy('sort_order')->orderBy('id');
    }

    public function faqs()
    {
        return $this->hasMany(PropertyFaq::class)->orderBy('sort_order')->orderBy('id');
    }

    public function enquiries()
    {
        return $this->hasMany(Enquiry::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }
}
