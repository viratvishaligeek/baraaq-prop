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
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $uploadedFiles = $this->file('gallery', []);
            $selectedIds = $this->input('gallery_media_ids', []);
            $uploaded = is_array($uploadedFiles) ? count($uploadedFiles) : 0;
            $selected = is_array($selectedIds) ? count($selectedIds) : 0;

            if ($uploaded + $selected > 20) {
                $validator->errors()->add('gallery', 'You can add up to 20 gallery images in total.');
            }
        });
    }

    public function rules(): array
    {
        return ['title' => ['required', 'string', 'max:255'], 'slug' => ['nullable', 'string', 'max:255', Rule::unique('properties', 'slug')->ignore($this->route('property'))], 'category_id' => ['nullable', Rule::exists('categories', 'id')->where('type', 'property')], 'property_type' => ['nullable', 'string', 'max:60'], 'listing_type' => ['required', Rule::in(['sale', 'rent', 'lease'])], 'price' => ['nullable', 'numeric', 'min:0'], 'price_label' => ['nullable', 'string', 'max:100'], 'bedrooms' => ['nullable', 'integer', 'min:0', 'max:100'], 'bathrooms' => ['nullable', 'integer', 'min:0', 'max:100'], 'area' => ['nullable', 'numeric', 'min:0'], 'area_unit' => ['required', 'string', 'max:20'], 'address' => ['nullable', 'string', 'max:255'], 'city' => ['nullable', 'string', 'max:100'], 'state' => ['nullable', 'string', 'max:100'], 'postal_code' => ['nullable', 'string', 'max:20'], 'country' => ['nullable', 'string', 'max:100'], 'availability_status' => ['sometimes', Rule::in(['available', 'under_offer', 'sold', 'rented', 'unavailable'])], 'focus_keyword' => ['nullable', 'string', 'max:160'], 'latitude' => ['nullable', 'numeric', 'between:-90,90'], 'longitude' => ['nullable', 'numeric', 'between:-180,180'], 'description' => ['nullable', 'string'], 'features' => ['nullable', 'array', 'max:50'], 'features.*' => ['required', 'string', 'max:120', 'distinct'], 'meta_title' => ['nullable', 'string', 'max:255'], 'meta_description' => ['nullable', 'string', 'max:1000'], 'og_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'], 'featured_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'], 'og_image_media_id' => ['nullable', 'integer', Rule::exists('media', 'id')->where(fn ($query) => $query->where('mime_type', 'like', 'image/%'))], 'featured_image_media_id' => ['nullable', 'integer', Rule::exists('media', 'id')->where(fn ($query) => $query->where('mime_type', 'like', 'image/%'))], 'gallery' => ['nullable', 'array', 'max:20'], 'gallery.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'], 'gallery_media_ids' => ['nullable', 'array', 'max:20'], 'gallery_media_ids.*' => ['integer', 'distinct', Rule::exists('media', 'id')->where(fn ($query) => $query->where('mime_type', 'like', 'image/%'))], 'status' => ['required', Rule::in(['draft', 'published', 'archived'])], 'is_featured' => ['sometimes', 'boolean'], 'cta_mode' => ['sometimes', Rule::in(['contact_form', 'whatsapp', 'both'])], 'whatsapp_number' => ['nullable', 'string', 'max:30', 'regex:/^[+]?[0-9(). -]{8,30}$/'], 'whatsapp_message' => ['nullable', 'string', 'max:500'], 'accommodations' => ['nullable', 'array'], 'accommodations.*.name' => ['required', 'string', 'max:160'], 'accommodations.*.image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'], 'accommodations.*.image_media_id' => ['nullable', 'integer', Rule::exists('media', 'id')->where(fn ($query) => $query->where('mime_type', 'like', 'image/%'))], 'accommodation_variants' => ['sometimes', 'array'], 'accommodation_variants.*.small' => ['sometimes', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'], 'accommodation_variants.*.medium' => ['sometimes', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'], 'accommodation_variants.*.large' => ['sometimes', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'], 'accommodations.*.category' => ['nullable', 'string', 'max:80'], 'accommodations.*.area' => ['nullable', 'numeric', 'min:0'], 'accommodations.*.area_unit' => ['nullable', 'string', 'max:20'], 'accommodations.*.description' => ['nullable', 'string'], 'accommodations.*.is_included' => ['sometimes', 'boolean'], 'accommodations.*.attributes' => ['nullable', 'array'], 'accommodations.*.attributes.*.key' => ['required', 'string', 'max:100'], 'accommodations.*.attributes.*.value' => ['nullable', 'string', 'max:500'], 'faqs' => ['nullable', 'array'], 'faqs.*.question' => ['required', 'string', 'max:500'], 'faqs.*.answer' => ['required', 'string']];
    }
}
