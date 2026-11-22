<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Homepage;
use Illuminate\Http\Response;

class HomeController extends Controller
{
    public function __invoke()
    {
        $homepage = Homepage::published()
            ->where('slug', 'home-9')
            ->with([
                'sections.reusableSection.mediaAttachments.media',
                'sections.mediaAttachments.media',
            ])
            ->first();

        abort_unless($homepage, 503, 'The fixed homepage is not published yet.');

        return view('frontend.home-builder', [
            'homepage' => $homepage,
            'isFixedHomepage' => true,
        ]);
    }
}
