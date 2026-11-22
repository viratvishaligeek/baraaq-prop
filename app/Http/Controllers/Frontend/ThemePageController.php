<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

class ThemePageController extends Controller
{
    private const PAGES = ['about', 'agency', 'agency-details', 'blog-grid-left-sidebar', 'blog-grid-right-sidebar', 'contact', 'error', 'faq', 'gallery', 'neighborhood-guide', 'pricing', 'service', 'service-details', 'service-right-sidebar', 'team', 'team-details'];
    public function show(string $template)
    {
        abort_unless(in_array($template, self::PAGES, true), 404);
        return view('frontend.theme-pages.' . $template);
    }
}
