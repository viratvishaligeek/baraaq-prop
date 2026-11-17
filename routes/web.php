<?php

use App\Http\Controllers\Frontend\ContentController as FrontendContentController;
use App\Http\Controllers\Frontend\EnquiryController as FrontendEnquiryController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\NewsletterController;
use App\Http\Controllers\Frontend\SitemapController;
use App\Http\Controllers\Frontend\PropertyController as FrontendPropertyController;
use App\Http\Controllers\Frontend\ThemePageController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/robots.txt', fn () => response("User-agent: *\\nAllow: /\\nDisallow: /admin\\n\\nSitemap: ".route('sitemap')."\\n", 200, ['Content-Type' => 'text/plain']))->name('robots');

Route::get('/properties', [FrontendPropertyController::class, 'index'])->name('properties.index');
Route::get('/properties/{property:slug}', [FrontendPropertyController::class, 'show'])->name('properties.show');
Route::post('/enquiries', [FrontendEnquiryController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('enquiries.store');

Route::post('/newsletter', [NewsletterController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('newsletter.store');

Route::get('/blog', [FrontendContentController::class, 'blog'])->name('blog.index');
Route::get('/blog/{post:slug}', [FrontendContentController::class, 'post'])->name('blog.show');
Route::get('/services', [FrontendContentController::class, 'services'])->name('services.index');
Route::get('/team', [FrontendContentController::class, 'team'])->name('team.index');
Route::get('/testimonials', [FrontendContentController::class, 'testimonials'])->name('testimonials.index');
Route::get('/services/{service:slug}', [FrontendContentController::class, 'service'])->name('services.show');
Route::post('/blog/{post:slug}/comments', [FrontendContentController::class, 'storeComment'])->middleware('throttle:3,10')->name('blog.comments.store');
Route::get('/pages/{page:slug}', [FrontendContentController::class, 'page'])->name('pages.show');

Route::get('/index.html', fn () => redirect()->route('home'));
Route::get('/property.html', fn () => redirect()->route('properties.index'));
Route::get('/blog.html', fn () => redirect()->route('blog.index'));
Route::get('/property-details.html', fn () => redirect()->route('properties.index'));
Route::get('/blog-details.html', fn () => redirect()->route('blog.index'));

Route::get('/{template}.html', [ThemePageController::class, 'show'])
    ->where('template', 'about|agency|agency-details|blog-grid-left-sidebar|blog-grid-right-sidebar|contact|error|faq|gallery|home-10|home-2|home-3|home-4|home-5|home-6|home-7|home-8|home-9|neighborhood-guide|pricing|service|service-details|service-right-sidebar|team|team-details')
    ->name('theme.page');
