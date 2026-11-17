<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsletterController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:190']
        ]);
        NewsletterSubscriber::firstOrCreate(['email' => Str::lower($data['email'])], ['subscribed_at' => now()]);
        return back()->with('newsletter_success', 'Thank you — your email has been subscribed.');
    }
}
