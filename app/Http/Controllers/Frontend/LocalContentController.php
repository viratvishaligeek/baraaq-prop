<?php
namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use App\Models\FaqItem;
use App\Models\GalleryItem;
use App\Models\NeighborhoodGuide;
class LocalContentController extends Controller {
 public function gallery(){$items=GalleryItem::published()->orderBy('sort_order')->latest()->paginate(18);return view('frontend.gallery.index',compact('items'));}
 public function faqs(){$faqs=FaqItem::query()->where('is_active',true)->orderBy('sort_order')->orderBy('id')->get();return view('frontend.faq.index',compact('faqs'));}
 public function neighborhoods(){$guides=NeighborhoodGuide::published()->orderBy('locality')->paginate(9);return view('frontend.neighborhood-guides.index',compact('guides'));}
 public function neighborhood(NeighborhoodGuide $guide){abort_unless($guide->status==='published'&&$guide->published_at&&$guide->published_at->lte(now()),404);return view('frontend.neighborhood-guides.show',compact('guide'));}
}