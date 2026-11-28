<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CircleRateAuditLog;
use Illuminate\Http\Request;

class CircleRateAuditLogController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'event' => ['nullable', 'string', 'max:60'],
            'subject_type' => ['nullable', 'string', 'max:120'],
            'from' => ['nullable', 'date'],
            'until' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $logs = CircleRateAuditLog::query()->with(['user:id,name,email', 'source:id,title'])
            ->when($filters['event'] ?? null, fn ($q, $event) => $q->where('event', $event))
            ->when($filters['subject_type'] ?? null, fn ($q, $type) => $q->where('subject_type', $type))
            ->when($filters['from'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
            ->when($filters['until'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '<=', $date))
            ->latest()->paginate(40)->withQueryString();

        return view('admin.circle-rates.audit.index', compact('logs', 'filters'));
    }
}
