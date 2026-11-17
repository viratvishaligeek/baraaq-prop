<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class TrackPageView
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (
            !$request->isMethod('GET')
            || $request->is('admin')
            || $request->is('admin/*')
            || $request->is('storage/*')
            || $request->is('build/*')
            || $request->is('theme-assets/*')
            || $request->is('sitemap.xml')
            || $request->is('robots.txt')
        ) {
            return $response;
        }

        // The cookie is the browser-facing identifier; the session fallback
        // keeps the same visitor stable when a client blocks or omits cookies.
        $visitor = $request->cookie('site_visitor') ?: $request->session()->get('site_visitor');
        $newVisitor = !is_string($visitor) || !preg_match('/^[a-f0-9]{64}$/', $visitor);
        if ($newVisitor) {
            $visitor = bin2hex(random_bytes(32));
        }
        $request->session()->put('site_visitor', $visitor);

        $visit = $request->cookie('site_visit') ?: $request->session()->get('site_visit');
        $newVisit = !is_string($visit) || !preg_match('/^[a-f0-9]{64}$/', $visit);
        if ($newVisit) {
            $visit = bin2hex(random_bytes(32));
        }
        $request->session()->put('site_visit', $visit);

        $route = $request->route();
        $routeName = $route?->getName();
        $contentType = match (true) {
            str_starts_with((string) $routeName, 'properties.show') => 'property',
            str_starts_with((string) $routeName, 'blog.show') => 'blog',
            str_starts_with((string) $routeName, 'services.show') => 'service',
            str_starts_with((string) $routeName, 'pages.show') => 'page',
            default => 'page',
        };
        $parameter = match ($contentType) {
            'property' => $route?->parameter('property'),
            'blog' => $route?->parameter('post'),
            'service' => $route?->parameter('service'),
            'page' => $route?->parameter('page'),
            default => null,
        };
        $contentId = is_object($parameter) && method_exists($parameter, 'getKey')
            ? $parameter->getKey()
            : null;

        DB::table('page_views')->insert([
            'visitor_hash' => hash('sha256', $visitor),
            'visit_hash' => hash('sha256', $visit),
            'path' => '/' . ltrim($request->path(), '/'),
            'route_name' => $routeName,
            'content_type' => $contentType,
            'content_id' => $contentId,
            'created_at' => now(),
        ]);

        if ($newVisitor) {
            $response->headers->setCookie(cookie('site_visitor', $visitor, 60 * 24 * 365, '/', null, $request->isSecure(), true, false, 'Lax'));
        }

        // Sliding 30-minute expiry means a visit ends after 30 minutes of inactivity.
        $response->headers->setCookie(cookie('site_visit', $visit, 30, '/', null, $request->isSecure(), true, false, 'Lax'));

        return $response;
    }
}
