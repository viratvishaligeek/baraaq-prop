<?php

use App\Http\Controllers\Frontend\ContentController as FrontendContentController;
use App\Http\Controllers\Frontend\EnquiryController as FrontendEnquiryController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\NewsletterController;
use App\Http\Controllers\Frontend\SitemapController;
use App\Http\Controllers\Frontend\LocalContentController;
use App\Http\Controllers\Frontend\PropertyController as FrontendPropertyController;
use App\Models\Setting;
use App\Models\Property;
use App\Models\Post;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::view('/contact', 'frontend.contact')->name('contact');
Route::get('/about', [FrontendContentController::class, 'about'])->name('about');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/robots.txt', function () {
    $configured = Setting::read('robots_rules', "User-agent: *\nAllow: /\nDisallow: /admin");
    $directives = collect(preg_split('/\r?\n/', (string) $configured))
        ->map(fn ($line) => trim($line))
        ->filter(fn ($line) => preg_match('/^(User-agent|Allow|Disallow|Crawl-delay)\s*:\s*[^\r\n]*$/i', $line))
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

Route::get('/gallery', [LocalContentController::class, 'gallery'])->name('gallery.index');
Route::get('/faq', [LocalContentController::class, 'faqs'])->name('faq.index');
Route::get('/neighborhood-guide', [LocalContentController::class, 'neighborhoods'])->name('neighborhood-guides.index');
Route::get('/neighborhood-guide/{guide:slug}', [LocalContentController::class, 'neighborhood'])->name('neighborhood-guides.show');
Route::get('/blog', [FrontendContentController::class, 'blog'])->name('blog.index');
Route::get('/pages/{page:slug}', [FrontendContentController::class, 'page'])->name('pages.show');
Route::get('/blog/{post:slug}', [FrontendContentController::class, 'post'])->name('blog.show');
Route::get('/services', [FrontendContentController::class, 'services'])->name('services.index');
Route::get('/team', [FrontendContentController::class, 'team'])->name('team.index');
Route::get('/testimonials', [FrontendContentController::class, 'testimonials'])->name('testimonials.index');
Route::get('/services/{service:slug}', [FrontendContentController::class, 'service'])->name('services.show');
Route::post('/blog/{post:slug}/comments', [FrontendContentController::class, 'storeComment'])->middleware('throttle:3,10')->name('blog.comments.store');

// Preserve the original HTML-template entry points while resolving detail pages
// against published database records instead of sending every detail URL to an index.
Route::get('/index.html', fn () => redirect()->route('home'));
Route::get('/home-9.html', fn () => redirect()->route('home'));
Route::get('/about.html', fn () => redirect()->route('about'));
Route::get('/contact.html', fn () => redirect()->route('contact'));
Route::get('/property.html', fn () => redirect()->route('properties.index'));
Route::get('/blog.html', fn () => redirect()->route('blog.index'));
Route::get('/gallery.html', fn () => redirect()->route('gallery.index'));
Route::get('/service.html', fn () => redirect()->route('services.index'));
Route::get('/faq.html', fn () => redirect()->route('faq.index'));
Route::get('/neighborhood-guide.html', fn () => redirect()->route('neighborhood-guides.index'));
Route::get('/property-details.html', function (Request $request) {
    $property = Property::query()->where('slug', $request->query('slug'))->firstOrFail();

    return redirect()->route('properties.show', $property);
});
Route::get('/blog-details.html', function (Request $request) {
    $post = Post::query()->where('slug', $request->query('slug'))->firstOrFail();

    return redirect()->route('blog.show', $post);
});
Route::get('/service-details.html', function (Request $request) {
    $service = Service::query()->where('slug', $request->query('slug'))->firstOrFail();

    return redirect()->route('services.show', $service);
});
Route::get('/error.html', fn () => response()->view('errors.404', [], 404))->name('error.page');

