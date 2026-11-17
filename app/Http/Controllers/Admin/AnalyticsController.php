<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);
        $from = Carbon::parse($filters['from'] ?? now()->subDays(29)->toDateString())->startOfDay();
        $to = Carbon::parse($filters['to'] ?? now()->toDateString())->endOfDay();

        $views = DB::table('page_views')->whereBetween('created_at', [$from, $to]);
        $enquiries = Enquiry::query()->whereBetween('created_at', [$from, $to]);

        $popularPages = (clone $views)->select('path', 'route_name', DB::raw('COUNT(*) as views'))
            ->groupBy('path', 'route_name')->orderByDesc('views')->limit(10)->get();

        $recentViews = (clone $views)->latest('created_at')->limit(10)->get();
        $recentEnquiries = (clone $enquiries)->latest()->limit(10)->get();

        return view('admin.analytics.index', [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'totalVisits' => (clone $views)->count(),
            'uniqueVisitors' => (clone $views)->distinct('visitor_hash')->count('visitor_hash'),
            'propertyViews' => (clone $views)->where('content_type', 'property')->count(),
            'blogViews' => (clone $views)->where('content_type', 'blog')->count(),
            'serviceViews' => (clone $views)->where('content_type', 'service')->count(),
            'propertyInquiries' => (clone $enquiries)->where('source', 'property')->count(),
            'contactQueries' => (clone $enquiries)->where('source', 'contact')->count(),
            'popularPages' => $popularPages,
            'recentViews' => $recentViews,
            'recentEnquiries' => $recentEnquiries,
        ]);
    }
}
