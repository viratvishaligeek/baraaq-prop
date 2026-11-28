<?php

use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\AppearanceController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\ContentPreviewController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EnquiryController;
use App\Http\Controllers\Admin\HomepageController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\MediaPickerController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\NewsletterController;
use App\Http\Controllers\Admin\PropertyController;
use App\Http\Controllers\Admin\PropertyTaxonomyController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SeoController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SiteContentController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'create'])->name('login');
        Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:5,1')->name('login.store');
    });

    Route::middleware(['auth', 'admin.access'])->group(function () {
        Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
        Route::get('/dashboard', DashboardController::class)->name('dashboard');\n        Route::get('/circle-rates', CircleRateDashboardController::class)->middleware('permission:circle-rates.view')->name('circle-rates.dashboard');
        Route::get('/analytics', [AnalyticsController::class, 'index'])->middleware('permission:analytics.view')->name('analytics.index');

        Route::get('/newsletters', [NewsletterController::class, 'index'])->middleware('permission:newsletters.manage')->name('newsletters.index');
        Route::delete('/newsletters/{subscriber}', [NewsletterController::class, 'destroy'])->middleware('permission:newsletters.manage')->name('newsletters.destroy');

        Route::get('/homepages', [HomepageController::class, 'index'])->middleware('permission:settings.manage')->name('homepages.index');
        Route::patch('/homepages/{homepage}', [HomepageController::class, 'update'])->middleware('permission:settings.manage')->name('homepages.update');
        Route::patch('/homepages/{homepage}/sections/{section}', [HomepageController::class, 'updateSection'])->middleware('permission:settings.manage')->name('homepages.sections.update');

        Route::get('/seo', [SeoController::class, 'index'])->middleware('permission:settings.manage')->name('seo.index');
        Route::get('/seo/indexing', [SeoController::class, 'indexing'])->middleware('permission:settings.manage')->name('seo.indexing');
        Route::get('/appearance', [AppearanceController::class, 'index'])->middleware('permission:settings.manage')->name('appearance.index');

        Route::get('/properties/types', [PropertyTaxonomyController::class, 'types'])->middleware('permission:properties.view')->name('properties.types');
        Route::get('/properties/amenities', [PropertyTaxonomyController::class, 'amenities'])->middleware('permission:properties.view')->name('properties.amenities');
        Route::resource('properties', PropertyController::class)->except(['show']);
        Route::delete('/properties/{property}/images/{image}', [PropertyController::class, 'destroyImage'])->scopeBindings()->name('properties.images.destroy');
        Route::patch('/properties/{property}/images/reorder', [PropertyController::class, 'reorderImages'])->name('properties.images.reorder');
        Route::patch('/properties/{property}/images/{image}/feature', [PropertyController::class, 'setFeaturedImage'])->scopeBindings()->name('properties.images.feature');

        Route::get('/enquiries', [EnquiryController::class, 'index'])->middleware('permission:enquiries.manage')->name('enquiries.index');
        Route::patch('/enquiries/{enquiry}', [EnquiryController::class, 'update'])->middleware('permission:enquiries.manage')->name('enquiries.update');
        Route::delete('/enquiries/{enquiry}', [EnquiryController::class, 'destroy'])->middleware('permission:enquiries.manage')->name('enquiries.destroy');

        Route::get('/comments', [CommentController::class, 'index'])->name('comments.index');
        Route::patch('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
        Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
        Route::patch('/comments/services/{serviceComment}', [CommentController::class, 'updateService'])->name('service-comments.update');
        Route::delete('/comments/services/{serviceComment}', [CommentController::class, 'destroyService'])->name('service-comments.destroy');

        Route::resource('tags', TagController::class)->except(['show'])->middleware('permission:tags.manage');
        Route::get('/content/{type}/{id}/preview', [ContentPreviewController::class, 'show'])->name('content.preview');
        Route::get('/content/{type}', [ContentController::class, 'index'])->name('content.index');
        Route::get('/content/{type}/create', [ContentController::class, 'create'])->name('content.create');
        Route::post('/content/{type}', [ContentController::class, 'store'])->name('content.store');
        Route::get('/content/{type}/{id}/edit', [ContentController::class, 'edit'])->name('content.edit');
        Route::put('/content/{type}/{id}', [ContentController::class, 'update'])->name('content.update');
        Route::delete('/content/{type}/{id}', [ContentController::class, 'destroy'])->name('content.destroy');

        Route::get('/site-content/{type}', [SiteContentController::class, 'index'])->name('site-content.index');
        Route::get('/site-content/{type}/create', [SiteContentController::class, 'create'])->name('site-content.create');
        Route::post('/site-content/{type}', [SiteContentController::class, 'store'])->name('site-content.store');
        Route::get('/site-content/{type}/{id}/edit', [SiteContentController::class, 'edit'])->name('site-content.edit');
        Route::put('/site-content/{type}/{id}', [SiteContentController::class, 'update'])->name('site-content.update');
        Route::delete('/site-content/{type}/{id}', [SiteContentController::class, 'destroy'])->name('site-content.destroy');

        Route::get('/media/picker', MediaPickerController::class)->name('media.picker');
        Route::post('/media/picker', [MediaPickerController::class, 'upload'])->name('media.picker.upload');
        Route::get('/media', [MediaController::class, 'index'])->middleware('permission:media.manage')->name('media.index');
        Route::post('/media', [MediaController::class, 'store'])->middleware('permission:media.manage')->name('media.store');
        Route::post('/media/{media}/edited-copy', [MediaController::class, 'editedCopy'])->middleware('permission:media.manage')->name('media.edited-copy');
        Route::patch('/media/{media}', [MediaController::class, 'update'])->middleware('permission:media.manage')->name('media.update');
        Route::delete('/media/bulk', [MediaController::class, 'bulkDestroy'])->middleware('permission:media.manage')->name('media.bulk-destroy');
        Route::delete('/media/{media}', [MediaController::class, 'destroy'])->middleware('permission:media.manage')->name('media.destroy');

        Route::get('/settings', [SettingsController::class, 'edit'])->middleware('permission:settings.manage')->name('settings.edit');
        Route::put('/settings', [SettingsController::class, 'update'])->middleware('permission:settings.manage')->name('settings.update');

        Route::resource('menus', MenuController::class)->except(['show'])->middleware('permission:menus.manage');
        Route::resource('roles', RoleController::class)->except(['show'])->middleware('permission:users.manage');
        Route::resource('users', UserController::class)->except(['show'])->middleware('permission:users.manage');
    });
});
