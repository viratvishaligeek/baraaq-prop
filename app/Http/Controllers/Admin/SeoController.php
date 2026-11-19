<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Post;
use App\Models\Property;
use App\Models\Service;
use Illuminate\Database\Eloquent\Builder;

class SeoController extends Controller
{
    public function index()
    {
        $needsSeo = static function (Builder $query): void {
            $query->where(function (Builder $missing) {
                $missing->whereNull('meta_title')
                    ->orWhere('meta_title', '')
                    ->orWhereNull('meta_description')
                    ->orWhere('meta_description', '')
                    ->orWhereNull('og_image')
                    ->orWhere('og_image', '');
            });
        };

        return view('admin.seo.index', [
            'properties' => Property::query()->select('id', 'title', 'slug', 'meta_title', 'meta_description', 'og_image')->where($needsSeo)->latest()->limit(25)->get(),
            'posts' => Post::query()->select('id', 'title', 'slug', 'meta_title', 'meta_description', 'canonical_url', 'og_image')->where($needsSeo)->latest()->limit(25)->get(),
            'services' => Service::query()->select('id', 'title', 'slug', 'meta_title', 'meta_description', 'canonical_url', 'og_image')->where($needsSeo)->latest()->limit(25)->get(),
            'pages' => Page::query()->select('id', 'title', 'slug', 'meta_title', 'meta_description', 'canonical_url', 'og_image')->where($needsSeo)->latest()->limit(25)->get(),
        ]);
    }
}
