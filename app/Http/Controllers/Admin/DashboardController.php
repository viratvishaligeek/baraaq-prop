<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Enquiry;
use App\Models\Page;
use App\Models\Post;
use App\Models\Property;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $recentViews = DB::table('page_views')
            ->where('created_at', '>=', now()->subDays(29)->startOfDay());

        return view('admin.dashboard', [
            'propertyCount' => Property::count(),
            'publishedPropertyCount' => Property::published()->count(),
            'postCount' => Post::count(),
            'pageCount' => Page::count(),
            'userCount' => User::count(),
            'newEnquiryCount' => Enquiry::where('status', 'new')->count(),
            'totalVisits' => (int) ((clone $recentViews)
                ->selectRaw('COUNT(DISTINCT COALESCE(visit_hash, visitor_hash)) as aggregate')
                ->value('aggregate') ?? 0),
            'uniqueVisitors' => (clone $recentViews)->distinct()->count('visitor_hash'),
            'totalPageViews' => (clone $recentViews)->count(),
            'recentActivities' => ActivityLog::query()
                ->with('causer:id,name')
                ->latest('created_at')
                ->limit(8)
                ->get(),
            'recentEnquiries' => Enquiry::with('property:id,title')->latest()->limit(8)->get(),
            'recentProperties' => Property::select('id', 'title', 'slug', 'status', 'price', 'city', 'created_at')
                ->latest()
                ->limit(6)
                ->get(),
        ]);
    }
}
