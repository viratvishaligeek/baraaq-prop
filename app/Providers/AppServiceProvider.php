<?php

namespace App\Providers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Enquiry;
use App\Models\Menu;
use App\Models\Page;
use App\Models\Post;
use App\Models\Property;
use App\Models\Service;
use App\Models\Setting;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Models\User;
use App\Observers\ActivityLogObserver;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('*', function ($view) {
            $view->with('siteSettings', Setting::allValues());
        });

        foreach ([
            Banner::class,
            Category::class,
            Comment::class,
            Enquiry::class,
            Menu::class,
            Page::class,
            Post::class,
            Property::class,
            Service::class,
            TeamMember::class,
            Testimonial::class,
            User::class,
        ] as $model) {
            $model::observe(ActivityLogObserver::class);
        }
    }
}
