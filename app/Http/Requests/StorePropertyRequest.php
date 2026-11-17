<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdministrator() || $this->user()?->hasPermission('properties.manage') || $this->user()?->hasPermission('properties.create') || $this->user()?->hasPermission('properties.edit');
    }
    public function rules(): array
    {
        return ['title' => ['required', 'string', 'max:255'], 'slug' => ['nullable', 'string', 'max:255', Rule::unique('properties', 'slug')->ignore($this->route('property'))], 'category_id' => ['nullable', Rule::exists('categories', 'id')->where('type', 'property')], 'property_type' => ['nullable', 'string', 'max:60'], 'listing_type' => ['required', Rule::in(['sale', 'rent', 'lease'])], 'price' => ['nullable', 'numeric', 'min:0'], 'price_label' => ['nullable', 'string', 'max:100'], 'bedrooms' => ['nullable', 'integer', 'min:0', 'max:100'], 'bathrooms' => ['nullable', 'integer', 'min:0', 'max:100'], 'area' => ['nullable', 'numeric', 'min:0'], 'area_unit' => ['required', 'string', 'max:20'], 'address' => ['nullable', 'string', 'max:255'], 'city' => ['nullable', 'string', 'max:100'], 'state' => ['nullable', 'string', 'max:100'], 'postal_code' => ['nullable', 'string', 'max:20'], 'latitude' => ['nullable', 'numeric', 'between:-90,90'], 'longitude' => ['nullable', 'numeric', 'between:-180,180'], 'description' => ['nullable', 'string'], 'meta_title' => ['nullable', 'string', 'max:255'], 'meta_description' => ['nullable', 'string', 'max:1000'], 'canonical_url' => ['nullable', 'url', 'max:2048'], 'og_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'], 'featured_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'], 'gallery' => ['nullable', 'array', 'max:20'], 'gallery.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'], 'status' => ['required', Rule::in(['draft', 'published', 'archived'])], 'is_featured' => ['sometimes', 'boolean'], 'cta_mode' => ['sometimes', Rule::in(['contact_form', 'whatsapp', 'both'])], 'whatsapp_number' => ['nullable', 'string', 'max:30', 'regex:/^[+]?[0-9(). -]{8,30}$/'], 'whatsapp_message' => ['nullable', 'string', 'max:500']];
    }
}
