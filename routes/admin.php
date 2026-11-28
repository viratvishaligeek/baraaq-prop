<?php

use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\AppearanceController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Admin\CircleRateDashboardController;
use App\Http\Controllers\Admin\CircleRateDemoDataController;
use App\Http\Controllers\Admin\CircleRateController;
use App\Http\Controllers\Admin\CircleRateMasterController;
use App\Http\Controllers\Admin\CircleRateSourceController;
use App\Http\Controllers\Admin\CircleRateImportController;
use App\Http\Controllers\Admin\CircleRateApiSettingsController;
use App\Http\Controllers\Admin\CircleRateValuationReportController;
use App\Http\Controllers\Admin\CircleRateAuditLogController;
use App\Http\Controllers\Admin\CircleRateSettingsController;
use App\Http\Controllers\Admin\CircleRateLocationImportController;
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
        Route::get('/dashboard', DashboardController::class)->name('dashboard');
        Route::get('/circle-rates/demo-data', [CircleRateDemoDataController::class, 'index'])->middleware('permission:circle-rates.view')->name('circle-rates.demo.index');
        Route::post('/circle-rates/demo-data/seed', [CircleRateDemoDataController::class, 'seed'])->middleware('permission:circle-rates.manage')->name('circle-rates.demo.seed');
        Route::post('/circle-rates/demo-data/reset', [CircleRateDemoDataController::class, 'reset'])->middleware('permission:circle-rates.manage')->name('circle-rates.demo.reset');
        Route::get('/circle-rates', CircleRateDashboardController::class)->middleware('permission:circle-rates.view')->name('circle-rates.dashboard');
        Route::get('/circle-rates/settings', [CircleRateSettingsController::class, 'index'])->middleware('permission:circle-rates.view')->name('circle-rates.settings.index');
        Route::put('/circle-rates/settings', [CircleRateSettingsController::class, 'update'])->middleware('permission:circle-rates.manage')->name('circle-rates.settings.update');
        Route::get('/circle-rates/reports', [CircleRateValuationReportController::class, 'index'])->middleware('permission:circle-rates.view')->name('circle-rates.reports.index');
        Route::get('/circle-rates/reports/export', [CircleRateValuationReportController::class, 'export'])->middleware('permission:circle-rates.view')->name('circle-rates.reports.export');
        Route::get('/circle-rates/audit-logs', [CircleRateAuditLogController::class, 'index'])->middleware('permission:circle-rates.view')->name('circle-rates.audit.index');
        Route::get('/circle-rates/api-settings', [CircleRateApiSettingsController::class, 'index'])->middleware('permission:circle-rates.view')->name('circle-rates.api.index');
        Route::post('/circle-rates/api-settings', [CircleRateApiSettingsController::class, 'store'])->middleware('permission:circle-rates.manage')->name('circle-rates.api.store');
        Route::put('/circle-rates/api-settings/{connection}', [CircleRateApiSettingsController::class, 'update'])->middleware('permission:circle-rates.manage')->name('circle-rates.api.update');
        Route::post('/circle-rates/api-settings/{connection}/test', [CircleRateApiSettingsController::class, 'testUnavailable'])->middleware('permission:circle-rates.manage')->name('circle-rates.api.test');
        Route::get('/circle-rates/sources', [CircleRateSourceController::class, 'index'])->middleware('permission:circle-rates.view')->name('circle-rates.sources.index');
        Route::post('/circle-rates/sources', [CircleRateSourceController::class, 'store'])->middleware('permission:circle-rates.manage')->name('circle-rates.sources.store');
        Route::delete('/circle-rates/sources/{source}', [CircleRateSourceController::class, 'destroy'])->middleware('permission:circle-rates.manage')->name('circle-rates.sources.destroy');
        Route::get('/circle-rates/imports', [CircleRateImportController::class, 'index'])->middleware('permission:circle-rates.view')->name('circle-rates.imports.index');
        Route::get('/circle-rates/locations/import', [CircleRateLocationImportController::class, 'index'])->middleware('permission:circle-rates.view')->name('circle-rates.locations.import.index');
        Route::post('/circle-rates/locations/import', [CircleRateLocationImportController::class, 'store'])->middleware('permission:circle-rates.import')->name('circle-rates.locations.import.store');
        Route::get('/circle-rates/locations/import/{batch}', [CircleRateLocationImportController::class, 'show'])->middleware('permission:circle-rates.view')->name('circle-rates.locations.import.show');
        Route::post('/circle-rates/locations/import/{batch}/preview', [CircleRateLocationImportController::class, 'preview'])->middleware('permission:circle-rates.import')->name('circle-rates.locations.import.preview');
        Route::post('/circle-rates/locations/import/{batch}/confirm', [CircleRateLocationImportController::class, 'confirm'])->middleware('permission:circle-rates.import')->name('circle-rates.locations.import.confirm');
        Route::post('/circle-rates/imports', [CircleRateImportController::class, 'upload'])->middleware('permission:circle-rates.import')->name('circle-rates.imports.upload');
        Route::get('/circle-rates/imports/{batch}', [CircleRateImportController::class, 'show'])->middleware('permission:circle-rates.view')->name('circle-rates.imports.show');
        Route::post('/circle-rates/imports/{batch}/preview', [CircleRateImportController::class, 'preview'])->middleware('permission:circle-rates.import')->name('circle-rates.imports.preview');
        Route::post('/circle-rates/imports/{batch}/confirm', [CircleRateImportController::class, 'confirm'])->middleware('permission:circle-rates.import')->name('circle-rates.imports.confirm');
        Route::get('/circle-rates/imports/{batch}/errors', [CircleRateImportController::class, 'errors'])->middleware('permission:circle-rates.view')->name('circle-rates.imports.errors');
        Route::get('/circle-rates/rates', [CircleRateController::class, 'index'])->middleware('permission:circle-rates.view')->name('circle-rates.rates.index');
        Route::get('/circle-rates/rates/create', [CircleRateController::class, 'create'])->middleware('permission:circle-rates.manage')->name('circle-rates.rates.create');
        Route::post('/circle-rates/rates', [CircleRateController::class, 'store'])->middleware('permission:circle-rates.manage')->name('circle-rates.rates.store');
        Route::post('/circle-rates/rates/bulk', [CircleRateController::class, 'bulk'])->middleware('permission:circle-rates.manage')->name('circle-rates.rates.bulk');
        Route::get('/circle-rates/rates/{circleRate}/edit', [CircleRateController::class, 'edit'])->middleware('permission:circle-rates.view')->name('circle-rates.rates.edit');
        Route::put('/circle-rates/rates/{circleRate}', [CircleRateController::class, 'update'])->middleware('permission:circle-rates.manage')->name('circle-rates.rates.update');
        Route::patch('/circle-rates/rates/{circleRate}/verify', [CircleRateController::class, 'verify'])->middleware('permission:circle-rates.verify')->name('circle-rates.rates.verify');
        Route::patch('/circle-rates/rates/{circleRate}/reject', [CircleRateController::class, 'reject'])->middleware('permission:circle-rates.verify')->name('circle-rates.rates.reject');
        Route::post('/circle-rates/rates/{circleRate}/duplicate', [CircleRateController::class, 'duplicate'])->middleware('permission:circle-rates.manage')->name('circle-rates.rates.duplicate');
        Route::delete('/circle-rates/rates/{circleRate}', [CircleRateController::class, 'destroy'])->middleware('permission:circle-rates.manage')->name('circle-rates.rates.destroy');
        Route::get('/circle-rates/{module}', [CircleRateMasterController::class, 'index'])->whereIn('module', ['districts', 'tehsils', 'area-types', 'location-levels', 'locations', 'property-types', 'categories', 'units', 'unit-conversions', 'financial-years', 'road-widths', 'rules', 'calculator-fields', 'field-options'])->middleware('permission:circle-rates.view')->name('circle-rates.master.index');
        Route::post('/circle-rates/{module}', [CircleRateMasterController::class, 'store'])->whereIn('module', ['districts', 'tehsils', 'area-types', 'location-levels', 'locations', 'property-types', 'categories', 'units', 'unit-conversions', 'financial-years', 'road-widths', 'rules', 'calculator-fields', 'field-options'])->middleware('permission:circle-rates.manage')->name('circle-rates.master.store');
        Route::put('/circle-rates/{module}/{id}', [CircleRateMasterController::class, 'update'])->whereIn('module', ['districts', 'tehsils', 'area-types', 'location-levels', 'locations', 'property-types', 'categories', 'units', 'unit-conversions', 'financial-years', 'road-widths', 'rules', 'calculator-fields', 'field-options'])->middleware('permission:circle-rates.manage')->name('circle-rates.master.update');
        Route::delete('/circle-rates/{module}/{id}', [CircleRateMasterController::class, 'destroy'])->whereIn('module', ['districts', 'tehsils', 'area-types', 'location-levels', 'locations', 'property-types', 'categories', 'units', 'unit-conversions', 'financial-years', 'road-widths', 'rules', 'calculator-fields', 'field-options'])->middleware('permission:circle-rates.manage')->name('circle-rates.master.destroy');
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
