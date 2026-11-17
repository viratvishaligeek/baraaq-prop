<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
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

        $recentActivities = ActivityLog::query()
            ->with('causer:id,name')
            ->whereBetween('created_at', [$from, $to])
            ->latest('created_at')
            ->limit(15)
            ->get();

        $popularPages = (clone $views)
            ->select('path', 'route_name', DB::raw('COUNT(*) as views'))
            ->groupBy('path', 'route_name')
            ->orderByDesc('views')
            ->limit(10)
            ->get();

        return view('admin.analytics.index', [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'totalVisits' => (int) ((clone $views)
                ->selectRaw('COUNT(DISTINCT COALESCE(visit_hash, visitor_hash)) as aggregate')
                ->value('aggregate') ?? 0),
            'uniqueVisitors' => (clone $views)->distinct()->count('visitor_hash'),
            'totalPageViews' => (clone $views)->count(),
            'propertyViews' => (clone $views)->where('content_type', 'property')->count(),
            'blogViews' => (clone $views)->where('content_type', 'blog')->count(),
            'serviceViews' => (clone $views)->where('content_type', 'service')->count(),
            'propertyInquiries' => (clone $enquiries)->where('source', 'property')->count(),
            'contactQueries' => (clone $enquiries)->where('source', 'contact')->count(),
            'popularPages' => $popularPages,
            'recentViews' => (clone $views)->latest('created_at')->limit(10)->get(),
            'recentEnquiries' => (clone $enquiries)->latest()->limit(10)->get(),
            'recentActivities' => $recentActivities,
        ]);
    }
}
