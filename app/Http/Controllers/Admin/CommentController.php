<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $comments = Comment::with('post:id,title,slug')
            ->when($request->filled('search'), function ($query) use ($request) {
                $term = $request->string('search');
                $query->where(fn ($where) => $where->where('name', 'like', '%'.$term.'%')
                    ->orWhere('email', 'like', '%'.$term.'%')
                    ->orWhere('body', 'like', '%'.$term.'%')
                    ->orWhereHas('post', fn ($post) => $post->where('title', 'like', '%'.$term.'%')));
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.comments.index', compact('comments'));
    }

    public function update(Request $request, Comment $comment)
    {
        $data = $request->validate(['status' => ['required', 'in:pending,approved,rejected']]);
        $comment->update($data);

        return back()->with('success', 'Comment moderation status updated.');
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();

        return back()->with('success', 'Comment deleted.');
    }
}
