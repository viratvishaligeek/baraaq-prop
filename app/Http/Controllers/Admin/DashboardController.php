<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Enquiry;
use App\Models\Page;
use App\Models\Post;
use App\Models\Property;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DashboardController extends Controller
{
    private function widgetDefinitions(): array
    {
        return [
            'properties' => ['label' => 'Properties', 'icon' => 'fa-house', 'value' => Property::count(), 'default' => 'properties', 'destinations' => ['properties' => ['label' => 'All properties', 'url' => route('admin.properties.index')]]],
            'published_properties' => ['label' => 'Published properties', 'icon' => 'fa-check-circle', 'value' => Property::published()->count(), 'default' => 'published_properties', 'destinations' => ['published_properties' => ['label' => 'Published properties', 'url' => route('admin.properties.index', ['status' => 'published'])], 'properties' => ['label' => 'All properties', 'url' => route('admin.properties.index')]]],
            'posts' => ['label' => 'Blog posts', 'icon' => 'fa-newspaper', 'value' => Post::count(), 'default' => 'posts', 'destinations' => ['posts' => ['label' => 'Blog posts', 'url' => route('admin.content.index', 'posts')]]],
            'pages' => ['label' => 'Pages', 'icon' => 'fa-file-lines', 'value' => Page::count(), 'default' => 'pages', 'destinations' => ['pages' => ['label' => 'Pages', 'url' => route('admin.content.index', 'pages')]]],
            'users' => ['label' => 'Users', 'icon' => 'fa-users', 'value' => User::count(), 'default' => 'users', 'destinations' => ['users' => ['label' => 'Users', 'url' => route('admin.users.index')]]],
            'new_enquiries' => ['label' => 'New enquiries', 'icon' => 'fa-inbox', 'value' => Enquiry::where('status', 'new')->count(), 'default' => 'new_enquiries', 'destinations' => ['new_enquiries' => ['label' => 'New enquiries', 'url' => route('admin.enquiries.index', ['status' => 'new'])], 'enquiries' => ['label' => 'All enquiries', 'url' => route('admin.enquiries.index')]]],
            'visits' => ['label' => 'Visits · 30 days', 'icon' => 'fa-chart-line', 'value' => 0, 'default' => 'analytics', 'destinations' => ['analytics' => ['label' => 'Analytics', 'url' => route('admin.analytics.index')]]],
            'page_views' => ['label' => 'Page views · 30 days', 'icon' => 'fa-eye', 'value' => 0, 'default' => 'analytics', 'destinations' => ['analytics' => ['label' => 'Analytics', 'url' => route('admin.analytics.index')]]],
        ];
    }

    public function manageWidgets()
    {
        return view('admin.dashboard-widgets', [
            'widgets' => $this->widgetDefinitions(),
            'configuration' => Setting::read('dashboard_widgets', []),
        ]);
    }

    public function updateWidgets(Request $request)
    {
        $definitions = $this->widgetDefinitions();
        $rules = ['widgets' => ['required', 'array']];
        foreach ($definitions as $key => $definition) {
            $rules['widgets.' . $key . '.enabled'] = ['required', 'boolean'];
            $rules['widgets.' . $key . '.destination'] = ['required', Rule::in(array_keys($definition['destinations']))];
            $rules['widgets.' . $key . '.order'] = ['required', 'integer', 'min:0', 'max:1000'];
        }

        $data = $request->validate($rules);
        $configuration = [];
        foreach ($definitions as $key => $definition) {
            $configuration[$key] = [
                'enabled' => (bool) $data['widgets'][$key]['enabled'],
                'destination' => $data['widgets'][$key]['destination'],
                'order' => (int) $data['widgets'][$key]['order'],
            ];
        }

        Setting::write('dashboard_widgets', $configuration, 'dashboard', 'json');

        return redirect()->route('admin.dashboard')->with('success', 'Dashboard widgets saved.');
    }

    public function __invoke()
    {
        $recentViews = DB::table('page_views')
            ->where('created_at', '>=', now()->subDays(29)->startOfDay());

        $dashboardWidgets = $this->widgetDefinitions();
        $configuration = Setting::read('dashboard_widgets', []);
        foreach ($dashboardWidgets as $key => &$widget) {
            $saved = $configuration[$key] ?? [];
            $destinationKey = $saved['destination'] ?? $widget['default'];
            $destination = $widget['destinations'][$destinationKey] ?? reset($widget['destinations']);
            $widget['url'] = $destination['url'];
            $widget['enabled'] = (bool) ($saved['enabled'] ?? true);
            $widget['order'] = (int) ($saved['order'] ?? 100);
        }
        unset($widget);

        $dashboardWidgets = collect($dashboardWidgets)
            ->filter(fn ($widget) => $widget['enabled'])
            ->sortBy('order')
            ->map(function ($widget) use ($recentViews) {
                if ($widget['label'] === 'Visits · 30 days') {
                    $widget['value'] = (int) ((clone $recentViews)
                        ->selectRaw('COUNT(DISTINCT COALESCE(visit_hash, visitor_hash)) as aggregate')
                        ->value('aggregate') ?? 0);
                } elseif ($widget['label'] === 'Page views · 30 days') {
                    $widget['value'] = (clone $recentViews)->count();
                }

                return $widget;
            })
            ->values();

        return view('admin.dashboard', [
            'dashboardWidgets' => $dashboardWidgets,
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
