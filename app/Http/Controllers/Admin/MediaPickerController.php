<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaPickerController extends Controller
{
    public function __invoke(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $media = Media::query()
            ->where('mime_type', 'like', 'image/%')
            ->when($search !== '', fn ($query) => $query->where('original_name', 'like', '%'.$search.'%'))
            ->latest()
            ->paginate(24);

        return response()->json([
            'data' => $media->getCollection()->map(fn (Media $item) => [
                'id' => $item->id,
                'name' => $item->original_name,
                'url' => Storage::disk($item->disk)->url($item->path),
                'alt' => $item->alt_text,
                'mime_type' => $item->mime_type,
                'size' => $item->size,
                'disk' => $item->disk,
            ]),
            'next_page_url' => $media->nextPageUrl(),
        ]);
    }
}
