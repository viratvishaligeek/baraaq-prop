<?php

use App\Http\Controllers\Admin\AppearanceController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EnquiryController;
use App\Http\Controllers\Admin\HomepageController;
use App\Http\Controllers\Admin\HomepageSectionController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\MediaPickerController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\NewsletterController;
use App\Http\Controllers\Admin\PageHomepageController;
use App\Http\Controllers\Admin\PageBuilderController;
use App\Http\Controllers\Admin\PropertyController;
use App\Http\Controllers\Admin\ReusableSectionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SeoController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\ServiceBuilderController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WidgetController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'create'])->name('login');
        Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:5,1')->name('login.store');
    });

    Route::middleware(['auth', 'admin.access'])->group(function () {
        Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
        Route::get('/dashboard', DashboardController::class)->name('dashboard');
        Route::get('/dashboard/widgets', [DashboardController::class, 'manageWidgets'])->middleware('permission:settings.manage')->name('dashboard.widgets.edit');
        Route::put('/dashboard/widgets', [DashboardController::class, 'updateWidgets'])->middleware('permission:settings.manage')->name('dashboard.widgets.update');
        Route::get('/analytics', [AnalyticsController::class, 'index'])->middleware('permission:analytics.view')->name('analytics.index');

        Route::get('/newsletters', [NewsletterController::class, 'index'])->middleware('permission:newsletters.manage')->name('newsletters.index');
        Route::delete('/newsletters/{subscriber}', [NewsletterController::class, 'destroy'])->middleware('permission:newsletters.manage')->name('newsletters.destroy');

        Route::get('/homepages', [HomepageController::class, 'index'])->middleware('permission:settings.manage')->name('homepages.index');
        Route::post('/homepages/{homepage}/duplicate', [HomepageController::class, 'duplicate'])->middleware('permission:settings.manage')->name('homepages.duplicate');
        Route::get('/homepages/{homepage}/builder', [HomepageController::class, 'builder'])->middleware('permission:settings.manage')->name('homepages.builder');
        Route::get('/homepages/{homepage}/preview', [HomepageController::class, 'preview'])->middleware('permission:settings.manage')->name('homepages.preview');
        Route::patch('/homepages/{homepage}/verify', [HomepageController::class, 'verifyComposition'])->middleware('permission:settings.manage')->name('homepages.verify');
        Route::patch('/homepages/{homepage}/publish', [HomepageController::class, 'publish'])->middleware('permission:settings.manage')->name('homepages.publish');
        Route::patch('/homepages/{homepage}/default', [HomepageController::class, 'setDefault'])->middleware('permission:settings.manage')->name('homepages.default');

        Route::post('/homepages/{homepage}/sections/{section}/save-reusable', [ReusableSectionController::class, 'storeFromSection'])->middleware('permission:settings.manage')->name('homepages.sections.save-reusable');
        Route::post('/homepages/{homepage}/reusable-sections/{reusableSection}', [ReusableSectionController::class, 'attach'])->middleware('permission:settings.manage')->name('homepages.reusable-sections.attach');
        Route::post('/homepages/{homepage}/sections', [HomepageSectionController::class, 'store'])->middleware('permission:settings.manage')->name('homepages.sections.store');
        Route::post('/homepages/{homepage}/sections/{section}/duplicate', [HomepageSectionController::class, 'duplicate'])->middleware('permission:settings.manage')->name('homepages.sections.duplicate');
        Route::patch('/homepages/{homepage}/sections/reorder', [HomepageSectionController::class, 'reorder'])->middleware('permission:settings.manage')->name('homepages.sections.reorder');
        Route::patch('/homepages/{homepage}/sections/{section}', [HomepageSectionController::class, 'update'])->middleware('permission:settings.manage')->name('homepages.sections.update');
        Route::patch('/homepages/{homepage}/sections/{section}/toggle', [HomepageSectionController::class, 'toggle'])->middleware('permission:settings.manage')->name('homepages.sections.toggle');
        Route::delete('/homepages/{homepage}/sections/{section}', [HomepageSectionController::class, 'destroy'])->middleware('permission:settings.manage')->name('homepages.sections.destroy');

        Route::get('/seo', [SeoController::class, 'index'])->middleware('permission:settings.manage')->name('seo.index');
        Route::get('/appearance', [AppearanceController::class, 'index'])->middleware('permission:settings.manage')->name('appearance.index');

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

        Route::patch('/pages/{page}/homepage', [PageHomepageController::class, 'store'])->middleware('permission:pages.manage')->name('pages.homepage');
        
        Route::get('/pages/{page}/builder', [PageBuilderController::class, 'edit'])->middleware('permission:pages.manage')->name('pages.builder');
        Route::post('/pages/{page}/builder/duplicate', [PageBuilderController::class, 'duplicate'])->middleware('permission:pages.manage')->name('pages.duplicate');
        Route::get('/pages/{page}/builder/preview', [PageBuilderController::class, 'preview'])->middleware('permission:pages.manage')->name('pages.preview');
        Route::post('/pages/{page}/widgets', [PageBuilderController::class, 'add'])->middleware('permission:pages.manage')->name('pages.widgets.add');
        Route::patch('/pages/{page}/widgets/reorder', [PageBuilderController::class, 'reorder'])->middleware('permission:pages.manage')->name('pages.widgets.reorder');
        Route::patch('/pages/{page}/widgets/{section}', [PageBuilderController::class, 'update'])->middleware('permission:pages.manage')->name('pages.widgets.update');
        Route::post('/pages/{page}/widgets/{section}/duplicate', [PageBuilderController::class, 'duplicateSection'])->middleware('permission:pages.manage')->name('pages.widgets.duplicate');
        Route::patch('/pages/{page}/widgets/{section}/toggle', [PageBuilderController::class, 'toggle'])->middleware('permission:pages.manage')->name('pages.widgets.toggle');
        Route::delete('/pages/{page}/widgets/{section}', [PageBuilderController::class, 'destroy'])->middleware('permission:pages.manage')->name('pages.widgets.delete');
        Route::patch('/pages/{page}/publish', [PageBuilderController::class, 'publish'])->middleware('permission:pages.manage')->name('pages.publish');

        Route::get('/services/{service}/builder', [ServiceBuilderController::class, 'edit'])->middleware('permission:services.manage')->name('services.builder');
        Route::get('/services/{service}/builder/preview', [ServiceBuilderController::class, 'preview'])->middleware('permission:services.manage')->name('services.preview');
        Route::post('/services/{service}/widgets', [ServiceBuilderController::class, 'add'])->middleware('permission:services.manage')->name('services.widgets.add');
        Route::patch('/services/{service}/widgets/reorder', [ServiceBuilderController::class, 'reorder'])->middleware('permission:services.manage')->name('services.widgets.reorder');
        Route::patch('/services/{service}/widgets/{section}', [ServiceBuilderController::class, 'update'])->middleware('permission:services.manage')->name('services.widgets.update');
        Route::post('/services/{service}/widgets/{section}/duplicate', [ServiceBuilderController::class, 'duplicate'])->middleware('permission:services.manage')->name('services.widgets.duplicate');
        Route::patch('/services/{service}/widgets/{section}/toggle', [ServiceBuilderController::class, 'toggle'])->middleware('permission:services.manage')->name('services.widgets.toggle');
        Route::delete('/services/{service}/widgets/{section}', [ServiceBuilderController::class, 'destroy'])->middleware('permission:services.manage')->name('services.widgets.delete');
        Route::patch('/services/{service}/publish', [ServiceBuilderController::class, 'publish'])->middleware('permission:services.manage')->name('services.publish');

        Route::get('/content/{type}', [ContentController::class, 'index'])->name('content.index');
        Route::get('/content/{type}/create', [ContentController::class, 'create'])->name('content.create');
        Route::post('/content/{type}', [ContentController::class, 'store'])->name('content.store');
        Route::get('/content/{type}/{id}/edit', [ContentController::class, 'edit'])->name('content.edit');
        Route::put('/content/{type}/{id}', [ContentController::class, 'update'])->name('content.update');
        Route::delete('/content/{type}/{id}', [ContentController::class, 'destroy'])->name('content.destroy');

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

        Route::patch('/widget-areas/{area}/reorder', [WidgetController::class, 'reorder'])->name('widget-areas.reorder')->middleware('permission:widgets.manage');
        Route::resource('widgets', WidgetController::class)->except(['show'])->middleware('permission:widgets.manage');
        Route::resource('menus', MenuController::class)->except(['show'])->middleware('permission:menus.manage');
        Route::resource('roles', RoleController::class)->except(['show'])->middleware('permission:users.manage');
        Route::resource('users', UserController::class)->except(['show'])->middleware('permission:users.manage');
    });
});
