<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Comment;
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

        $dashboardCards = collect([
            ['label' => 'Properties', 'icon' => 'fa-house', 'value' => Property::count(), 'url' => route('admin.properties.index')],
            ['label' => 'Published properties', 'icon' => 'fa-check-circle', 'value' => Property::published()->count(), 'url' => route('admin.properties.index', ['status' => 'published'])],
            ['label' => 'Blog posts', 'icon' => 'fa-newspaper', 'value' => Post::count(), 'url' => route('admin.content.index', 'posts')],
            ['label' => 'Pages', 'icon' => 'fa-file-lines', 'value' => Page::count(), 'url' => route('admin.content.index', 'pages')],
            ['label' => 'Users', 'icon' => 'fa-users', 'value' => User::count(), 'url' => route('admin.users.index')],
            ['label' => 'Total enquiries', 'icon' => 'fa-envelope', 'value' => Enquiry::count(), 'url' => route('admin.enquiries.index')],
            ['label' => 'Pending enquiries', 'icon' => 'fa-inbox', 'value' => Enquiry::where('status', 'new')->count(), 'url' => route('admin.enquiries.index', ['status' => 'new'])],
            ['label' => 'Comments', 'icon' => 'fa-comments', 'value' => Comment::count(), 'url' => route('admin.comments.index')],
            ['label' => 'Pending comments', 'icon' => 'fa-comment-dots', 'value' => Comment::where('status', 'pending')->count(), 'url' => route('admin.comments.index', ['status' => 'pending'])],
            ['label' => 'Visits · 30 days', 'icon' => 'fa-chart-line', 'value' => (int) ((clone $recentViews)->selectRaw('COUNT(DISTINCT COALESCE(visit_hash, visitor_hash)) as aggregate')->value('aggregate') ?? 0), 'url' => route('admin.analytics.index')],
            ['label' => 'Page views · 30 days', 'icon' => 'fa-eye', 'value' => (clone $recentViews)->count(), 'url' => route('admin.analytics.index')],
        ]);

        return view('admin.dashboard', [
            'dashboardCards' => $dashboardCards,
            'propertyCount' => Property::count(),
            'publishedPropertyCount' => Property::published()->count(),
            'postCount' => Post::count(),
            'pageCount' => Page::count(),
            'userCount' => User::count(),
            'newEnquiryCount' => Enquiry::where('status', 'new')->count(),
            'totalVisits' => (int) ((clone $recentViews)->selectRaw('COUNT(DISTINCT COALESCE(visit_hash, visitor_hash)) as aggregate')->value('aggregate') ?? 0),
            'uniqueVisitors' => (clone $recentViews)->distinct()->count('visitor_hash'),
            'totalPageViews' => (clone $recentViews)->count(),
            'recentActivities' => ActivityLog::query()->with('causer:id,name')->latest('created_at')->limit(8)->get(),
            'recentEnquiries' => Enquiry::with('property:id,title')->latest()->limit(8)->get(),
            'recentProperties' => Property::select('id', 'title', 'slug', 'status', 'price', 'city', 'created_at')->latest()->limit(6)->get(),
        ]);
    }
}
