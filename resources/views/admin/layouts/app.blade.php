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
    <link href="{{ asset('admin/css/admin.css') }}" rel="stylesheet">@stack('styles')
</head>

<body>
    <div class="admin-shell">
        <aside class="sidebar">
            <a class="brand" href="{{ route('admin.dashboard') }}">{{ config('app.name') }}</a>
            <div class="nav-label">WORKSPACE</div>
            <a href="{{ route('admin.dashboard') }}"
                class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-gauge">
                </i> Dashboard</a>
            @if (auth()->user()->isAdministrator() || auth()->user()->hasPermission('analytics.view'))
                <a href="{{ route('admin.analytics.index') }}"
                    class="{{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-line">
                    </i> Analytics</a>
            @endif
            @if (auth()->user()->isAdministrator() ||
                    auth()->user()->hasPermission('properties.manage') ||
                    auth()->user()->hasPermission('properties.view') ||
                    auth()->user()->hasPermission('properties.create') ||
                    auth()->user()->hasPermission('properties.edit'))
                <a href="{{ route('admin.properties.index') }}"
                    class="{{ request()->routeIs('admin.properties.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-house">
                    </i> Properties</a>
            @endif
            @if (auth()->user()->isAdministrator() || auth()->user()->hasPermission('enquiries.manage'))
                <a href="{{ route('admin.enquiries.index') }}"
                    class="{{ request()->routeIs('admin.enquiries.*') ? 'active' : '' }}">
                    <i class="fa-regular fa-message">
                    </i> Enquiries</a>
            @endif
            @if (auth()->user()->isAdministrator() ||
                    auth()->user()->hasPermission('posts.manage') ||
                    auth()->user()->hasPermission('posts.view') ||
                    auth()->user()->hasPermission('posts.create') ||
                    auth()->user()->hasPermission('posts.edit'))
                <a href="{{ route('admin.content.index', 'posts') }}">
                    <i class="fa-regular fa-newspaper">
                    </i> Blog posts</a>
            @endif
            @if (auth()->user()->isAdministrator() ||
                    auth()->user()->hasPermission('pages.manage') ||
                    auth()->user()->hasPermission('pages.view') ||
                    auth()->user()->hasPermission('pages.create') ||
                    auth()->user()->hasPermission('pages.edit'))
                <a href="{{ route('admin.content.index', 'pages') }}">
                    <i class="fa-regular fa-file-lines">
                    </i> Pages</a>
            @endif
            @if (auth()->user()->isAdministrator() ||
                    auth()->user()->hasPermission('categories.manage') ||
                    auth()->user()->hasPermission('categories.view') ||
                    auth()->user()->hasPermission('categories.create') ||
                    auth()->user()->hasPermission('categories.edit'))
                <a href="{{ route('admin.content.index', 'categories') }}">
                    <i class="fa-solid fa-tags">
                    </i> Categories</a>
            @endif
            @if (auth()->user()->isAdministrator() ||
                    auth()->user()->hasPermission('banners.manage') ||
                    auth()->user()->hasPermission('banners.view') ||
                    auth()->user()->hasPermission('banners.create') ||
                    auth()->user()->hasPermission('banners.edit'))
                <a href="{{ route('admin.content.index', 'banners') }}">
                    <i class="fa-regular fa-images">
                    </i> Banners</a>
            @endif
            @if (auth()->user()->isAdministrator() ||
                    auth()->user()->hasPermission('team.manage') ||
                    auth()->user()->hasPermission('team.view') ||
                    auth()->user()->hasPermission('team.create') ||
                    auth()->user()->hasPermission('team.edit'))
                <a href="{{ route('admin.content.index', 'team') }}">
                    <i class="fa-solid fa-users">
                    </i> Team members</a>
            @endif
            @if (auth()->user()->isAdministrator() ||
                    auth()->user()->hasPermission('services.manage') ||
                    auth()->user()->hasPermission('services.view') ||
                    auth()->user()->hasPermission('services.create') ||
                    auth()->user()->hasPermission('services.edit'))
                <a href="{{ route('admin.content.index', 'services') }}">
                    <i class="fa-solid fa-briefcase">
                    </i> Services</a>
            @endif
            @if (auth()->user()->isAdministrator() ||
                    auth()->user()->hasPermission('testimonials.manage') ||
                    auth()->user()->hasPermission('testimonials.view') ||
                    auth()->user()->hasPermission('testimonials.create') ||
                    auth()->user()->hasPermission('testimonials.edit'))
                <a href="{{ route('admin.content.index', 'testimonials') }}">
                    <i class="fa-solid fa-star">
                    </i> Testimonials</a>
            @endif
            @if (auth()->user()->isAdministrator() ||
                    auth()->user()->hasPermission('comments.manage') ||
                    auth()->user()->hasPermission('comments.view') ||
                    auth()->user()->hasPermission('comments.view'))
                <a href="{{ route('admin.comments.index') }}"
                    class="{{ request()->routeIs('admin.comments.*') ? 'active' : '' }}">
                    <i class="fa-regular fa-comments">
                    </i> Comments</a>
            @endif
            @if (auth()->user()->isAdministrator() || auth()->user()->hasPermission('widgets.manage'))
                <a href="{{ route('admin.widgets.index') }}"
                    class="{{ request()->routeIs('admin.widgets.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-puzzle-piece">
                    </i> Widgets</a>
            @endif
            @if (auth()->user()->isAdministrator() || auth()->user()->hasPermission('media.manage'))
                <a href="{{ route('admin.media.index') }}">
                    <i class="fa-regular fa-folder-open">
                    </i> Media library</a>
            @endif
            @if (auth()->user()->isAdministrator() || auth()->user()->hasPermission('newsletters.manage'))
                <a href="{{ route('admin.newsletters.index') }}"
                    class="{{ request()->routeIs('admin.newsletters.*') ? 'active' : '' }}">
                    <i class="fa-regular fa-envelope">
                    </i> Newsletter subscribers</a>
            @endif
            @if (auth()->user()->isAdministrator() || auth()->user()->hasPermission('settings.manage'))
                <div class="nav-label">WEBSITE</div>
                <a href="{{ route('admin.appearance.index') }}"
                    class="{{ request()->routeIs('admin.appearance.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-palette">
                    </i> Appearance & themes</a>
                <a href="{{ route('admin.homepages.index') }}"
                    class="{{ request()->routeIs('admin.homepages.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-layer-group">
                    </i> Homepage designs</a>
                <a href="{{ route('admin.menus.index') }}"
                    class="{{ request()->routeIs('admin.menus.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-list">
                    </i> Navigation menus</a>
                <a href="{{ route('admin.seo.index') }}"
                    class="{{ request()->routeIs('admin.seo.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-magnifying-glass">
                    </i> SEO</a>
                <a href="{{ route('admin.settings.edit') }}"
                    class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-gear">
                    </i> Website settings</a>
            @endif
            @if (auth()->user()->isAdministrator() || auth()->user()->hasPermission('users.manage'))
                <div class="nav-label">ACCESS CONTROL</div>
                <a href="{{ route('admin.roles.index') }}"
                    class="{{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-shield-halved">
                    </i> Roles & permissions</a>
                <a href="{{ route('admin.users.index') }}"
                    class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-user-gear">
                    </i> Users</a>
            @endif
            <div class="nav-label">ACCOUNT</div>
            <form method="POST" action="{{ route('admin.logout') }}">@csrf<button class="nav-logout">
                    <i class="fa-solid fa-arrow-right-from-bracket">
                    </i> Sign out</button>
            </form>
        </aside>
        <main class="main-panel">
            <header class="topbar">
                <span class="text-muted small">Content management</span>
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
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('admin/js/admin.js') }}"></script>@stack('scripts')
    @if (session('success'))
        <script>
            if (window.toastr) toastr.success(@json(session('success')));
        </script>
    @endif
</body>

</html>
