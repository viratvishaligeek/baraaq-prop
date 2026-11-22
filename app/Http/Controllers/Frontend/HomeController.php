<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Homepage;

class HomeController extends Controller
{
    public function __invoke()
    {
        $homepage = Homepage::published()
            ->where('slug', 'home-9')
            ->with([
                'sections.mediaAttachments.media',
            ])
            ->first();

        abort_unless($homepage, 503, 'The fixed homepage is not published yet.');

        return view('frontend.homepage', [
            'homepage' => $homepage,
        ]);
    }
}
