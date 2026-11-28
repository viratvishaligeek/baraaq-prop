<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CircleRateSource;
use App\Models\CircleRateAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CircleRateSourceController extends Controller
{
    public function index()
    {
        $sources = CircleRateSource::query()->withCount('rates')->latest()->paginate(20);
        return view('admin.circle-rates.sources.index', compact('sources'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:220'],
            'reference_number' => ['nullable', 'string', 'max:120'],
            'source_url' => ['nullable', 'url', 'max:1000'],
            'published_on' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:10000'],
            'document' => ['nullable', 'file', 'mimes:pdf,xlsx,xls,csv,jpg,jpeg,png', 'max:20480'],
        ]);

        if ($request->hasFile('document')) {
            $path = $request->file('document')->store('circle-rate-sources', 'public');
            if (! $path) {
                return back()->withErrors(['document' => 'The source document could not be stored.'])->withInput();
            }
            $data['document_path'] = $path;
            $data['document_disk'] = 'public';
        }

        unset($data['document']);
        $data['uploaded_by'] = $request->user()->id;
        $source = CircleRateSource::query()->create($data);
        CircleRateAuditLog::query()->create([
            'user_id' => $request->user()->id,
            'subject_type' => CircleRateSource::class,
            'subject_id' => $source->id,
            'event' => 'source_created',
            'new_values' => $source->getAttributes(),
            'reason' => 'Source document uploaded or registered',
            'source_id' => $source->id,
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Source document saved.');
    }

    public function destroy(CircleRateSource $source)
    {
        if ($source->rates()->exists()) {
            return back()->withErrors(['source' => 'This source is linked to circle rates. Replace the source reference before deleting it.']);
        }

        $oldValues = $source->getAttributes();
        if ($source->document_path) {
            Storage::disk($source->document_disk)->delete($source->document_path);
        }
        $source->delete();
        CircleRateAuditLog::query()->create([
            'user_id' => request()->user()->id,
            'subject_type' => CircleRateSource::class,
            'subject_id' => $source->id,
            'event' => 'source_deleted',
            'old_values' => $oldValues,
            'reason' => 'Unlinked source document deleted',
            'ip_address' => request()->ip(),
        ]);

        return back()->with('success', 'Source document deleted.');
    }
}
