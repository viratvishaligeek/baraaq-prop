<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EnquiryController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'property_id' => ['nullable', Rule::exists('properties', 'id')->where(fn($query) => $query->where('status', 'published')->whereNotNull('published_at')->where('published_at', '<=', now()))],
            'name' => ['required', 'string', 'max:150'],
            'subject' => ['nullable', 'string', 'max:120'],
            'email' => ['nullable', 'required_without:phone', 'email', 'max:190'],
            'phone' => ['nullable', 'required_without:email', 'string', 'max:30'],
            'preferred_date' => ['nullable', 'date', 'after_or_equal:today'],
            'preferred_time' => ['nullable', 'string', 'max:40'],
            'message' => ['nullable', 'string', 'max:5000'],
        ]);

        $data['source'] = ! empty($data['property_id']) ? 'property' : 'contact';
        $data['status'] = 'new';

        Enquiry::create($data);

        return back()->with('enquiry_success', 'Thank you. Your enquiry has been received.');
    }
}
