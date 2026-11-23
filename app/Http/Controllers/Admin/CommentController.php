<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\ServiceComment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    private function authorizeAction(string $action): void
    {
        $user = request()->user();

        abort_unless(
            $user && ($user->isAdministrator() || $user->hasPermission('comments.manage') || $user->hasPermission('comments.' . $action)),
            403
        );
    }

    public function index(Request $request)
    {
        $this->authorizeAction('view');

        $comments = Comment::with('post:id,title,slug')
            ->when($request->filled('search'), function ($query) use ($request) {
                $term = $request->string('search');
                $query->where(fn($where) => $where->where('name', 'like', '%' . $term . '%')
                    ->orWhere('email', 'like', '%' . $term . '%')
                    ->orWhere('body', 'like', '%' . $term . '%')
                    ->orWhereHas('post', fn($post) => $post->where('title', 'like', '%' . $term . '%')));
            })
            ->when($request->filled('status'), fn($query) => $query->where('status', $request->input('status')))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $serviceComments = ServiceComment::with('service:id,title,slug')
            ->when($request->filled('search'), function ($query) use ($request) {
                $term = $request->string('search');
                $query->where(fn ($where) => $where->where('name', 'like', '%'.$term.'%')
                    ->orWhere('email', 'like', '%'.$term.'%')
                    ->orWhere('body', 'like', '%'.$term.'%')
                    ->orWhereHas('service', fn ($service) => $service->where('title', 'like', '%'.$term.'%')));
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->latest()
            ->paginate(25, ['*'], 'service_comments_page')
            ->withQueryString();

        return view('admin.comments.index', compact('comments', 'serviceComments'));
    }

    public function update(Request $request, Comment $comment)
    {
        $data = $request->validate([
            'status' => ['sometimes', 'required', 'in:pending,approved,rejected'],
            'name' => ['sometimes', 'required', 'string', 'max:120'],
            'email' => ['sometimes', 'required', 'email', 'max:190'],
            'body' => ['sometimes', 'required', 'string', 'min:5', 'max:5000'],
        ]);

        abort_if($data === [], 422, 'No comment changes were supplied.');

        if (array_key_exists('status', $data)) {
            $this->authorizeAction($data['status'] === 'approved' ? 'publish' : 'edit');
        }

        if (array_intersect(['name', 'email', 'body'], array_keys($data)) !== []) {
            $this->authorizeAction('edit');
        }

        $comment->update($data);

        return back()->with('success', 'Comment updated. Moderation status and edits have been saved.');
    }

    public function updateService(Request $request, ServiceComment $serviceComment)
    {
        $data = $request->validate([
            'status' => ['sometimes', 'required', 'in:pending,approved,rejected'],
            'name' => ['sometimes', 'required', 'string', 'max:120'],
            'email' => ['sometimes', 'required', 'email', 'max:190'],
            'body' => ['sometimes', 'required', 'string', 'min:5', 'max:5000'],
        ]);

        abort_if($data === [], 422, 'No comment changes were supplied.');

        if (array_key_exists('status', $data)) {
            $this->authorizeAction($data['status'] === 'approved' ? 'publish' : 'edit');
        }
        if (array_intersect(['name', 'email', 'body'], array_keys($data)) !== []) {
            $this->authorizeAction('edit');
        }

        $serviceComment->update($data);

        return back()->with('success', 'Service comment updated.');
    }

    public function destroyService(ServiceComment $serviceComment)
    {
        $this->authorizeAction('delete');
        $serviceComment->delete();

        return back()->with('success', 'Service comment deleted.');
    }

    public function destroy(Comment $comment)
    {
        $this->authorizeAction('delete');
        $comment->delete();

        return back()->with('success', 'Comment deleted.');
    }
}
