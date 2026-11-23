<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;

class NewsletterController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate(['q' => ['nullable', 'string', 'max:160']]);
        $subscribers = NewsletterSubscriber::query()
            ->when($filters['q'] ?? null, fn ($query, $term) => $query->where('email', 'like', '%'.$term.'%'))
            ->latest('subscribed_at')->paginate(30)->withQueryString();
        return view('admin.newsletters.index', compact('subscribers'));
    }
    public function destroy(NewsletterSubscriber $subscriber)
    {
        $subscriber->delete();
        return back()->with('success', 'Subscriber removed.');
    }
}
