<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Post;
use App\Models\Property;
class SeoController extends Controller
{
    public function index()
    {
        return view('admin.seo.index',[
            'properties'=>Property::select('id','title','slug','meta_title','meta_description')->where(fn($q)=>$q->where(fn($m)=>$m->whereNull('meta_title')->orWhere('meta_title','')->orWhereNull('meta_description')->orWhere('meta_description','')))->latest()->limit(10)->get(),
            'posts'=>Post::select('id','title','slug','meta_title','meta_description')->where(fn($q)=>$q->whereNull('meta_title')->orWhereNull('meta_description'))->latest()->limit(10)->get(),
            'services'=>Service::select('id','title','slug','meta_title','meta_description')->where(fn($q)=>$q->whereNull('meta_title')->orWhereNull('meta_description'))->latest()->limit(10)->get(),
            'pages'=>Page::select('id','title','slug','meta_title','meta_description')->where(fn($q)=>$q->whereNull('meta_title')->orWhereNull('meta_description'))->latest()->limit(10)->get(),
        ]);
    }
}