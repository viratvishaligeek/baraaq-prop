<?php

use App\Http\Controllers\Frontend\ContentController as FrontendContentController;
use App\Http\Controllers\Frontend\CircleRateCalculatorController;
use App\Http\Controllers\Frontend\EnquiryController as FrontendEnquiryController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\LocalContentController;
use App\Http\Controllers\Frontend\NewsletterController;
use App\Http\Controllers\Frontend\PropertyController as FrontendPropertyController;
use App\Http\Controllers\Frontend\SitemapController;
use App\Models\Setting;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/contact', [FrontendContentController::class, 'contact'])->name('contact');
Route::get('/bareilly-property-circle-rate-calculator', [CircleRateCalculatorController::class, 'index'])->name('circle-rates.calculator');
Route::get('/circle-rate-data/tehsils', [CircleRateCalculatorController::class, 'tehsils'])->middleware('throttle:120,1')->name('circle-rates.data.tehsils');
Route::get('/circle-rate-data/locations', [CircleRateCalculatorController::class, 'locations'])->middleware('throttle:120,1')->name('circle-rates.data.locations');
Route::get('/circle-rate-data/categories', [CircleRateCalculatorController::class, 'categories'])->middleware('throttle:120,1')->name('circle-rates.data.categories');
Route::get('/circle-rate-data/road-widths', [CircleRateCalculatorController::class, 'roadWidths'])->middleware('throttle:120,1')->name('circle-rates.data.road-widths');
Route::post('/bareilly-property-circle-rate-calculator', [CircleRateCalculatorController::class, 'calculate'])->middleware('throttle:10,1')->name('circle-rates.calculate');
Route::get('/circle-rate-valuation/{reference}', [CircleRateCalculatorController::class, 'result'])->name('circle-rates.result');
Route::get('/about', [FrontendContentController::class, 'about'])->name('about');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/robots.txt', function () {
    $configured = Setting::read('robots_rules', "User-agent: *\nAllow: /\nDisallow: /admin");
    $directives = collect(preg_split('/\r?\n/', (string) $configured))
        ->map(fn($line) => trim($line))
        ->filter(fn($line) => preg_match('/^(User-agent|Allow|Disallow|Crawl-delay)\s*:\s*[^\r\n]*$/i', $line))
        ->values()
        ->all();
    $directives[] = '';
    $directives[] = 'Sitemap: ' . route('sitemap');

    return response(implode(PHP_EOL, $directives) . PHP_EOL, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
})->name('robots');

Route::get('/properties', [FrontendPropertyController::class, 'index'])->name('properties.index');
Route::get('/properties/{property:slug}', [FrontendPropertyController::class, 'show'])->name('properties.show');
Route::post('/enquiries', [FrontendEnquiryController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('enquiries.store');

Route::post('/newsletter', [NewsletterController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('newsletter.store');

Route::controller(LocalContentController::class)->group(function () {
    Route::get('/gallery', 'gallery')->name('gallery.index');
    Route::get('/faq', 'faqs')->name('faq.index');
    Route::get('/neighborhood-guide', 'neighborhoods')->name('neighborhood-guides.index');
    Route::get('/neighborhood-guide/{guide:slug}', 'neighborhood')->name('neighborhood-guides.show');
});
Route::controller(FrontendContentController::class)->group(function () {
    Route::get('/blog',  'blog')->name('blog.index');
    Route::get('/pages/{page:slug}', 'page')->name('pages.show');
    Route::get('/blog/{post:slug}', 'post')->name('blog.show');
    Route::get('/services', 'services')->name('services.index');
    Route::get('/team', 'team')->name('team.index');
    Route::get('/testimonials', 'testimonials')->name('testimonials.index');
    Route::get('/services/{service:slug}', 'service')->name('services.show');
    Route::post('/blog/{post:slug}/comments', 'storeComment')->middleware('throttle:3,10')->name('blog.comments.store');
    Route::post('/services/{service:slug}/comments', 'storeServiceComment')->middleware('throttle:3,10')->name('services.comments.store');
});
