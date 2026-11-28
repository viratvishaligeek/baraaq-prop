<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') · {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/3.0.7/css/responsive.bootstrap5.min.css" rel="stylesheet">
    <link href="{{ asset('admin/css/admin.css') }}" rel="stylesheet">@stack('styles')
</head>

<body>
    <div class="admin-shell">
        <aside class="sidebar" id="admin-sidebar">
            <a class="brand" href="{{ route('admin.dashboard') }}">{{ config('app.name') }}</a>
            <div class="nav-label">CMS WORKSPACE</div>
            <details class="sidebar-group" @if (request()->routeIs('admin.dashboard') || request()->routeIs('admin.analytics.*')) open @endif>
                <summary>
                    <i class="fa-solid fa-gauge">
                    </i> Dashboard
                </summary>
                <a class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                    href="{{ route('admin.dashboard') }}">Overview</a>
                @if (auth()->user()->isAdministrator() || auth()->user()->hasPermission('analytics.view'))
                    <a class="sidebar-link {{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}"
                        href="{{ route('admin.analytics.index') }}">Analytics</a>
                @endif
            </details>
            @if (auth()->user()->isAdministrator() || auth()->user()->hasPermission('circle-rates.manage') || auth()->user()->hasPermission('circle-rates.view'))
                <details class="sidebar-group" @if (request()->routeIs('admin.circle-rates.*')) open @endif>
                    <summary><i class="fa-solid fa-calculator"></i> Property Circle Rate Management</summary>
                    <a class="sidebar-link {{ request()->routeIs('admin.circle-rates.dashboard') ? 'active' : '' }}" href="{{ route('admin.circle-rates.dashboard') }}">Dashboard</a>
                    <a class="sidebar-link {{ request()->routeIs('admin.circle-rates.rates.*') ? 'active' : '' }}" href="{{ route('admin.circle-rates.rates.index') }}">Circle rate records</a>
                    <a class="sidebar-link {{ request()->routeIs('admin.circle-rates.sources.*') ? 'active' : '' }}" href="{{ route('admin.circle-rates.sources.index') }}">Source documents</a>
                    @foreach ([
                        'districts' => 'Districts',
                        'tehsils' => 'Tehsils',
                        'area-types' => 'Area types',
                        'location-levels' => 'Location levels',
                        'locations' => 'Locations',
                        'property-types' => 'Property types',
                        'categories' => 'Property categories',
                        'units' => 'Rate units',
                        'road-widths' => 'Road width ranges',
                        'financial-years' => 'Financial years',
                    ] as $circleRateModule => $circleRateLabel)
                        <a class="sidebar-link {{ request()->route('module') === $circleRateModule ? 'active' : '' }}" href="{{ route('admin.circle-rates.master.index', $circleRateModule) }}">{{ $circleRateLabel }}</a>
                    @endforeach
                </details>
            @endif
            @if (auth()->user()->isAdministrator() ||
                    auth()->user()->hasPermission('properties.manage') ||
                    auth()->user()->hasPermission('properties.view') ||
                    auth()->user()->hasPermission('properties.create') ||
                    auth()->user()->hasPermission('properties.edit') ||
                    auth()->user()->hasPermission('enquiries.manage'))
                <details class="sidebar-group" @if (request()->routeIs('admin.properties.*') || request()->routeIs('admin.enquiries.*')) open @endif>
                    <summary>
                        <i class="fa-solid fa-house">
                        </i> Properties
                    </summary>
                    @if (auth()->user()->isAdministrator() ||
                            auth()->user()->hasPermission('properties.manage') ||
                            auth()->user()->hasPermission('properties.view') ||
                            auth()->user()->hasPermission('properties.create') ||
                            auth()->user()->hasPermission('properties.edit'))
                        <a class="sidebar-link {{ request()->routeIs('admin.properties.index') || request()->routeIs('admin.properties.edit') ? 'active' : '' }}"
                            href="{{ route('admin.properties.index') }}">All properties</a>
                        @if (auth()->user()->isAdministrator() ||
                                auth()->user()->hasPermission('properties.manage') ||
                                auth()->user()->hasPermission('properties.create'))
                            <a class="sidebar-link {{ request()->routeIs('admin.properties.create') ? 'active' : '' }}"
                                href="{{ route('admin.properties.create') }}">Add new property</a>
                        @endif
                        <a class="sidebar-link {{ request()->route('type') === 'categories' && request('category_type') === 'property' ? 'active' : '' }}"
                            href="{{ route('admin.content.index', 'categories') }}?category_type=property">Property
                            categories</a>
                        <a class="sidebar-link {{ request()->routeIs('admin.properties.types') ? 'active' : '' }}"
                            href="{{ route('admin.properties.types') }}">Property types</a>
                        <a class="sidebar-link {{ request()->routeIs('admin.properties.amenities') ? 'active' : '' }}"
                            href="{{ route('admin.properties.amenities') }}">Property amenities</a>
                    @endif
                    @if (auth()->user()->isAdministrator() || auth()->user()->hasPermission('enquiries.manage'))
                        <a class="sidebar-link {{ request()->routeIs('admin.enquiries.*') ? 'active' : '' }}"
                            href="{{ route('admin.enquiries.index') }}">Property enquiries</a>
                    @endif
                </details>
            @endif
            <details class="sidebar-group" @if (request()->routeIs('admin.site-content.*')) open @endif>
                <summary>
                    <i class="fa-regular fa-images">
                    </i> Local content
                </summary>
                @foreach (['gallery' => 'Gallery', 'faqs' => 'FAQs', 'neighborhood-guides' => 'Neighborhood guides'] as $contentType => $label)
                    @if (auth()->user()->isAdministrator() ||
                            auth()->user()->hasPermission($contentType . '.manage') ||
                            auth()->user()->hasPermission($contentType . '.view'))
                        <a class="sidebar-link {{ request()->route('type') === $contentType ? 'active' : '' }}"
                            href="{{ route('admin.site-content.index', $contentType) }}">{{ $label }}</a>
                    @endif
                @endforeach
            </details>
            <details class="sidebar-group" @if (request()->routeIs('admin.content.*') ||
                    request()->routeIs('admin.comments.*') ||
                    request()->routeIs('admin.tags.*')) open @endif>
                <summary>
                    <i class="fa-regular fa-file-lines">
                    </i> Content management
                </summary>
                @if (auth()->user()->isAdministrator() ||
                        auth()->user()->hasPermission('posts.manage') ||
                        auth()->user()->hasPermission('posts.view') ||
                        auth()->user()->hasPermission('posts.create') ||
                        auth()->user()->hasPermission('posts.edit'))
                    <a class="sidebar-link {{ request()->route('type') === 'posts' ? 'active' : '' }}"
                        href="{{ route('admin.content.index', 'posts') }}">Blog posts</a>
                @endif
                @if (auth()->user()->isAdministrator() ||
                        auth()->user()->hasPermission('pages.manage') ||
                        auth()->user()->hasPermission('pages.view') ||
                        auth()->user()->hasPermission('pages.create') ||
                        auth()->user()->hasPermission('pages.edit'))
                    <a class="sidebar-link {{ request()->route('type') === 'pages' ? 'active' : '' }}"
                        href="{{ route('admin.content.index', 'pages') }}">Pages</a>
                @endif
                @if (auth()->user()->isAdministrator() ||
                        auth()->user()->hasPermission('categories.manage') ||
                        auth()->user()->hasPermission('categories.view') ||
                        auth()->user()->hasPermission('categories.create') ||
                        auth()->user()->hasPermission('categories.edit'))
                    <a class="sidebar-link {{ request()->route('type') === 'categories' ? 'active' : '' }}"
                        href="{{ route('admin.content.index', 'categories') }}">Categories</a>
                @endif
                @if (auth()->user()->isAdministrator() || auth()->user()->hasPermission('tags.manage'))
                    <a class="sidebar-link {{ request()->routeIs('admin.tags.*') ? 'active' : '' }}"
                        href="{{ route('admin.tags.index') }}">Tags</a>
                @endif
                @if (auth()->user()->isAdministrator() ||
                        auth()->user()->hasPermission('services.manage') ||
                        auth()->user()->hasPermission('services.view') ||
                        auth()->user()->hasPermission('services.create') ||
                        auth()->user()->hasPermission('services.edit'))
                    <a class="sidebar-link {{ request()->route('type') === 'services' ? 'active' : '' }}"
                        href="{{ route('admin.content.index', 'services') }}">Services</a>
                @endif
                @if (auth()->user()->isAdministrator() ||
                        auth()->user()->hasPermission('team.manage') ||
                        auth()->user()->hasPermission('team.view') ||
                        auth()->user()->hasPermission('team.create') ||
                        auth()->user()->hasPermission('team.edit'))
                    <a class="sidebar-link {{ request()->route('type') === 'team' ? 'active' : '' }}"
                        href="{{ route('admin.content.index', 'team') }}">Team members</a>
                @endif
                @if (auth()->user()->isAdministrator() ||
                        auth()->user()->hasPermission('testimonials.manage') ||
                        auth()->user()->hasPermission('testimonials.view') ||
                        auth()->user()->hasPermission('testimonials.create') ||
                        auth()->user()->hasPermission('testimonials.edit'))
                    <a class="sidebar-link {{ request()->route('type') === 'testimonials' ? 'active' : '' }}"
                        href="{{ route('admin.content.index', 'testimonials') }}">Testimonials</a>
                @endif
                @if (auth()->user()->isAdministrator() ||
                        auth()->user()->hasPermission('comments.manage') ||
                        auth()->user()->hasPermission('comments.view'))
                    <a class="sidebar-link {{ request()->routeIs('admin.comments.*') ? 'active' : '' }}"
                        href="{{ route('admin.comments.index') }}">Comments</a>
                @endif
            </details>
            @if (auth()->user()->isAdministrator() ||
                    auth()->user()->hasPermission('settings.manage') ||
                    auth()->user()->hasPermission('menus.manage'))
                <details class="sidebar-group" @if (request()->routeIs('admin.appearance.*') ||
                        request()->routeIs('admin.homepages.*') ||
                        request()->routeIs('admin.menus.*')) open @endif>
                    <summary>
                        <i class="fa-solid fa-palette">
                        </i> Appearance & design
                    </summary>
                    @if (auth()->user()->isAdministrator() || auth()->user()->hasPermission('settings.manage'))
                        <a class="sidebar-link {{ request()->routeIs('admin.appearance.*') ? 'active' : '' }}"
                            href="{{ route('admin.appearance.index') }}">Theme & appearance</a>
                        <a class="sidebar-link {{ request()->routeIs('admin.homepages.*') ? 'active' : '' }}"
                            href="{{ route('admin.homepages.index') }}">Homepage management</a>
                    @endif
                    @if (auth()->user()->isAdministrator() || auth()->user()->hasPermission('menus.manage'))
                        <a class="sidebar-link {{ request()->routeIs('admin.menus.*') ? 'active' : '' }}"
                            href="{{ route('admin.menus.index') }}">Navigation menus</a>
                    @endif
                </details>
            @endif
            @if (auth()->user()->isAdministrator() || auth()->user()->hasPermission('media.manage'))
                <details class="sidebar-group" @if (request()->routeIs('admin.media.*')) open @endif>
                    <summary>
                        <i class="fa-regular fa-images">
                        </i> Media
                    </summary>
                    <a class="sidebar-link {{ request()->routeIs('admin.media.index') && request('type') !== 'images' ? 'active' : '' }}"
                        href="{{ route('admin.media.index') }}">Media library</a>
                    <a class="sidebar-link {{ request()->routeIs('admin.media.index') && request('type') === 'images' ? 'active' : '' }}"
                        href="{{ route('admin.media.index', ['type' => 'images']) }}">Image management</a>
                </details>
            @endif
            @if (auth()->user()->isAdministrator() || auth()->user()->hasPermission('settings.manage'))
                <details class="sidebar-group" @if (request()->routeIs('admin.seo.*')) open @endif>
                    <summary>
                        <i class="fa-solid fa-magnifying-glass">
                        </i> SEO management
                    </summary>
                    <a class="sidebar-link {{ request()->routeIs('admin.seo.*') ? 'active' : '' }}"
                        href="{{ route('admin.seo.index') }}">Website SEO settings</a>
                    <a class="sidebar-link" href="{{ route('admin.settings.edit') }}#seo-settings">SEO
                        configuration</a>
                    <a class="sidebar-link {{ request()->routeIs('admin.seo.indexing') ? 'active' : '' }}"
                        href="{{ route('admin.seo.indexing') }}">Sitemap / indexing settings</a>
                </details>
            @endif
            @if (auth()->user()->isAdministrator() ||
                    auth()->user()->hasPermission('settings.manage') ||
                    auth()->user()->hasPermission('users.manage') ||
                    auth()->user()->hasPermission('newsletters.manage'))
                <details class="sidebar-group" @if (request()->routeIs('admin.settings.*') ||
                        request()->routeIs('admin.newsletters.*') ||
                        request()->routeIs('admin.roles.*') ||
                        request()->routeIs('admin.users.*')) open @endif>
                    <summary>
                        <i class="fa-solid fa-gear">
                        </i> Settings
                    </summary>
                    @if (auth()->user()->isAdministrator() || auth()->user()->hasPermission('settings.manage'))
                        <a class="sidebar-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}"
                            href="{{ route('admin.settings.edit') }}#general-settings">General settings</a>
                    @endif
                    @if (auth()->user()->isAdministrator() || auth()->user()->hasPermission('newsletters.manage'))
                        <a class="sidebar-link {{ request()->routeIs('admin.newsletters.*') ? 'active' : '' }}"
                            href="{{ route('admin.newsletters.index') }}">Newsletter subscribers</a>
                    @endif
                    @if (auth()->user()->isAdministrator() || auth()->user()->hasPermission('users.manage'))
                        <a class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                            href="{{ route('admin.users.index') }}">User management</a>
                        <a class="sidebar-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}"
                            href="{{ route('admin.roles.index') }}">Admin settings</a>
                    @endif
                </details>
            @endif
            <div class="nav-label">ACCOUNT</div>
            <form method="POST" action="{{ route('admin.logout') }}">@csrf<button class="nav-logout">
                    <i class="fa-solid fa-arrow-right-from-bracket">
                    </i> Sign out</button>
            </form>
        </aside>
        <button type="button" class="sidebar-backdrop" aria-label="Close navigation" data-sidebar-close></button>
        <main class="main-panel">
            <header class="topbar">
                <div class="d-flex align-items-center gap-3">
                    <button type="button" class="btn btn-outline-secondary sidebar-toggle d-md-none" aria-controls="admin-sidebar" aria-expanded="false" aria-label="Open navigation" data-sidebar-toggle><i class="fa-solid fa-bars"></i></button>
                    <span class="text-muted small">Content management</span>
                </div>
                <span class="small fw-semibold">{{ auth()->user()->name }}</span>
            </header>
            <div class="page-content">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @yield('content')
            </div>
        </main>
    </div>
    @include('admin.partials.media-picker')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.7/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.7/js/responsive.bootstrap5.min.js"></script>
    <script>
        (function () {
            const shell = document.querySelector('.admin-shell');
            const toggle = document.querySelector('[data-sidebar-toggle]');
            const closeControls = document.querySelectorAll('[data-sidebar-close]');
            const sidebar = document.getElementById('admin-sidebar');
            if (!shell || !toggle || !sidebar) return;
            function setSidebarOpen(open) {
                shell.classList.toggle('sidebar-open', open);
                document.body.classList.toggle('sidebar-open', open);
                toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
                toggle.setAttribute('aria-label', open ? 'Close navigation' : 'Open navigation');
            }
            toggle.addEventListener('click', function () {
                setSidebarOpen(!shell.classList.contains('sidebar-open'));
            });
            closeControls.forEach(function (control) {
                control.addEventListener('click', function () { setSidebarOpen(false); });
            });
            sidebar.querySelectorAll('a').forEach(function (link) {
                link.addEventListener('click', function () {
                    if (window.matchMedia('(max-width: 767.98px)').matches) setSidebarOpen(false);
                });
            });
            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') setSidebarOpen(false);
            });
            window.addEventListener('resize', function () {
                if (window.innerWidth >= 768) setSidebarOpen(false);
            });
        })();
    </script>
    <script src="{{ asset('admin/js/admin.js') }}"></script>
    @include('admin.partials.rich-text-editor')
    <script src="{{ asset('admin/js/slug-generator.js') }}"></script>
    <script src="{{ asset('admin/js/seo-preview.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (!window.DataTable) return;
            document.querySelectorAll('table[data-datatable]').forEach(function(table) {
                if (table.tHead && !table.querySelector('tbody td[colspan]') && !DataTable.isDataTable(
                        table)) {
                    new DataTable(table, {
                        pageLength: 10,
                        paging: table.dataset.datatablePaging !== 'false',
                        info: table.dataset.datatablePaging !== 'false',
                        lengthChange: table.dataset.datatablePaging !== 'false',
                        lengthMenu: [10, 25, 50, 100],
                        searching: table.dataset.datatableSearching !== 'false',
                        ordering: true,
                        responsive: true,
                        autoWidth: false,
                        language: {
                            emptyTable: 'No records found.',
                            search: 'Search:',
                            searchPlaceholder: 'Search records...'
                        }
                    });
                }
            });
        });
    </script>
    @stack('scripts')
    @if (session('success'))
        <script>
            if (window.toastr) toastr.success(@json(session('success')));
        </script>
    @endif
</body>

</html>
