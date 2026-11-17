<?php

use App\Http\Controllers\Admin\AppearanceController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EnquiryController;
use App\Http\Controllers\Admin\HomepageController;
use App\Http\Controllers\Admin\HomepageSectionController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\NewsletterController;
use App\Http\Controllers\Admin\PropertyController;
use App\Http\Controllers\Admin\ReusableSectionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SeoController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'create'])->name('login');
        Route::post('/login', [AuthController::class, 'store'])
            ->middleware('throttle:5,1')
            ->name('login.store');
    });

    Route::middleware(['auth', 'admin.access'])->group(function () {
        Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
        Route::get('/', DashboardController::class)->name('dashboard');

        Route::get('/newsletters', [NewsletterController::class, 'index'])
            ->middleware('permission:newsletters.manage')
            ->name('newsletters.index');
        Route::delete('/newsletters/{subscriber}', [NewsletterController::class, 'destroy'])
            ->middleware('permission:newsletters.manage')
            ->name('newsletters.destroy');

        Route::get('/homepages', [HomepageController::class, 'index'])
            ->middleware('permission:settings.manage')
            ->name('homepages.index');
        Route::post('/homepages/{homepage}/duplicate', [HomepageController::class, 'duplicate'])
            ->middleware('permission:settings.manage')
            ->name('homepages.duplicate');
        Route::get('/homepages/{homepage}/builder', [HomepageController::class, 'builder'])
            ->middleware('permission:settings.manage')
            ->name('homepages.builder');
        Route::get('/homepages/{homepage}/preview', [HomepageController::class, 'preview'])
            ->middleware('permission:settings.manage')
            ->name('homepages.preview');

        Route::patch('/homepages/{homepage}/publish', [HomepageController::class, 'publish'])
            ->middleware('permission:settings.manage')
            ->name('homepages.publish');
        Route::patch('/homepages/{homepage}/default', [HomepageController::class, 'setDefault'])
            ->middleware('permission:settings.manage')
            ->name('homepages.default');

        Route::post('/homepages/{homepage}/sections/{section}/save-reusable', [ReusableSectionController::class, 'storeFromSection'])
            ->middleware('permission:settings.manage')
            ->name('homepages.sections.save-reusable');
        Route::post('/homepages/{homepage}/reusable-sections/{reusableSection}', [ReusableSectionController::class, 'attach'])
            ->middleware('permission:settings.manage')
            ->name('homepages.reusable-sections.attach');

        Route::post('/homepages/{homepage}/sections', [HomepageSectionController::class, 'store'])
            ->middleware('permission:settings.manage')
            ->name('homepages.sections.store');
        Route::post('/homepages/{homepage}/sections/{section}/duplicate', [HomepageSectionController::class, 'duplicate'])
            ->middleware('permission:settings.manage')
            ->name('homepages.sections.duplicate');

        Route::patch('/homepages/{homepage}/sections/reorder', [HomepageSectionController::class, 'reorder'])
            ->middleware('permission:settings.manage')
            ->name('homepages.sections.reorder');
        Route::patch('/homepages/{homepage}/sections/{section}', [HomepageSectionController::class, 'update'])
            ->middleware('permission:settings.manage')
            ->name('homepages.sections.update');

        Route::patch('/homepages/{homepage}/sections/{section}/toggle', [HomepageSectionController::class, 'toggle'])
            ->middleware('permission:settings.manage')
            ->name('homepages.sections.toggle');
        Route::delete('/homepages/{homepage}/sections/{section}', [HomepageSectionController::class, 'destroy'])
            ->middleware('permission:settings.manage')
            ->name('homepages.sections.destroy');

        Route::get('/seo', [SeoController::class, 'index'])
            ->middleware('permission:settings.manage')
            ->name('seo.index');
        Route::get('/appearance', [AppearanceController::class, 'index'])
            ->middleware('permission:settings.manage')
            ->name('appearance.index');

        Route::resource('properties', PropertyController::class)
            ->except(['show'])
            ->middleware('permission:properties.manage');
        Route::delete('/properties/{property}/images/{image}', [PropertyController::class, 'destroyImage'])
            ->scopeBindings()
            ->middleware('permission:properties.manage')
            ->name('properties.images.destroy');
        Route::patch('/properties/{property}/images/reorder', [PropertyController::class, 'reorderImages'])
            ->middleware('permission:properties.manage')
            ->name('properties.images.reorder');
        Route::patch('/properties/{property}/images/{image}/feature', [PropertyController::class, 'setFeaturedImage'])
            ->scopeBindings()
            ->middleware('permission:properties.manage')
            ->name('properties.images.feature');

        Route::get('/enquiries', [EnquiryController::class, 'index'])
            ->middleware('permission:enquiries.manage')
            ->name('enquiries.index');
        Route::patch('/enquiries/{enquiry}', [EnquiryController::class, 'update'])
            ->middleware('permission:enquiries.manage')
            ->name('enquiries.update');
        Route::delete('/enquiries/{enquiry}', [EnquiryController::class, 'destroy'])
            ->middleware('permission:enquiries.manage')
            ->name('enquiries.destroy');

        Route::get('/content/{type}', [ContentController::class, 'index'])->name('content.index');
        Route::get('/content/{type}/create', [ContentController::class, 'create'])->name('content.create');
        Route::post('/content/{type}', [ContentController::class, 'store'])->name('content.store');
        Route::get('/content/{type}/{id}/edit', [ContentController::class, 'edit'])->name('content.edit');
        Route::put('/content/{type}/{id}', [ContentController::class, 'update'])->name('content.update');
        Route::delete('/content/{type}/{id}', [ContentController::class, 'destroy'])->name('content.destroy');

        Route::get('/media/picker', MediaPickerController::class)
            ->middleware('permission:media.manage')
            ->name('media.picker');
        Route::get('/media', [MediaController::class, 'index'])
            ->middleware('permission:media.manage')
            ->name('media.index');
        Route::post('/media', [MediaController::class, 'store'])
            ->middleware('permission:media.manage')
            ->name('media.store');
        Route::patch('/media/{media}', [MediaController::class, 'update'])
            ->middleware('permission:media.manage')
            ->name('media.update');
        Route::delete('/media/{media}', [MediaController::class, 'destroy'])
            ->middleware('permission:media.manage')
            ->name('media.destroy');

        Route::get('/settings', [SettingsController::class, 'edit'])
            ->middleware('permission:settings.manage')
            ->name('settings.edit');
        Route::put('/settings', [SettingsController::class, 'update'])
            ->middleware('permission:settings.manage')
            ->name('settings.update');

        Route::resource('menus', MenuController::class)
            ->except(['show'])
            ->middleware('permission:menus.manage');
        Route::resource('roles', RoleController::class)
            ->except(['show'])
            ->middleware('permission:users.manage');
        Route::resource('users', UserController::class)
            ->except(['show'])
            ->middleware('permission:users.manage');
    });
});
