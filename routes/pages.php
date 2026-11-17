<?php

use App\Http\Controllers\Frontend\ContentController;
use Illuminate\Support\Facades\Route;

// Keep the catch-all CMS page route last so it cannot shadow admin routes.
Route::get('/{page:slug}', [ContentController::class, 'page'])->name('pages.show');
