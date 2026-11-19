<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TagController extends Controller
{
    private function authorizeTags(): void
    {
        $user = request()->user();
        abort_unless($user && ($user->isAdministrator() || $user->hasPermission('tags.manage')), 403);
    }

    public function index()
    {
        $this->authorizeTags();
        return view('admin.tags.index', ['tags' => Tag::withCount('posts')->orderBy('name')->paginate(25)]);
    }

    public function create()
    {
        $this->authorizeTags();
        return view('admin.tags.form', ['tag' => new Tag()]);
    }

    public function store(Request $request)
    {
        $this->authorizeTags();
        $data = $request->validate(['name' => ['required', 'string', 'max:120'], 'slug' => ['nullable', 'string', 'max:140', 'unique:tags,slug']]);
        $data['slug'] = $this->uniqueSlug($data['slug'] ?: $data['name']);
        Tag::create($data);
        return redirect()->route('admin.tags.index')->with('success', 'Tag created.');
    }

    public function edit(Tag $tag)
    {
        $this->authorizeTags();
        return view('admin.tags.form', compact('tag'));
    }

    public function update(Request $request, Tag $tag)
    {
        $this->authorizeTags();
        $data = $request->validate(['name' => ['required', 'string', 'max:120'], 'slug' => ['nullable', 'string', 'max:140', Rule::unique('tags', 'slug')->ignore($tag->id)]]);
        $data['slug'] = $this->uniqueSlug($data['slug'] ?: $data['name'], $tag->id);
        $tag->update($data);
        return redirect()->route('admin.tags.index')->with('success', 'Tag updated.');
    }

    public function destroy(Tag $tag)
    {
        $this->authorizeTags();
        $tag->delete();
        return back()->with('success', 'Tag deleted. Blog posts remain intact.');
    }

    private function uniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $base = Str::slug($value) ?: Str::random(8);
        $slug = $base;
        $suffix = 2;
        while (Tag::query()->where('slug', $slug)->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))->exists()) {
            $slug = $base . '-' . $suffix++;
        }
        return $slug;
    }
}
