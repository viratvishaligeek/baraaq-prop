<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EnquiryController extends Controller
{
    public function index(Request $request)
    {
        $enquiries = Enquiry::with('property:id,title,slug')
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->when($request->filled('source'), fn ($query) => $query->where('source', $request->input('source')))
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->string('q')->trim().'%';
                $query->where(function ($search) use ($term) {
                    $search->where('name', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhere('phone', 'like', $term)
                        ->orWhere('message', 'like', $term)
                        ->orWhereHas('property', fn ($property) => $property->where('title', 'like', $term));
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.enquiries.index', [
            'enquiries' => $enquiries,
            'filters' => $request->only(['status', 'source', 'q']),
        ]);
    }

    public function update(Request $request, Enquiry $enquiry)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['new', 'contacted', 'qualified', 'closed', 'spam'])],
            'admin_notes' => ['nullable', 'string', 'max:5000'],
        ]);

        if ($data['status'] === 'contacted' && ! $enquiry->contacted_at) {
            $data['contacted_at'] = now();
        }

        $enquiry->update($data);

        return back()->with('success', 'Enquiry updated.');
    }

    public function destroy(Enquiry $enquiry)
    {
        $enquiry->delete();

        return back()->with('success', 'Enquiry deleted.');
    }
}
