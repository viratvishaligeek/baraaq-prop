<?php
namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use App\Models\FaqItem;
use App\Models\GalleryItem;
use App\Models\NeighborhoodGuide;
use Illuminate\Http\Request;
class LocalContentController extends Controller {
 public function gallery(Request $request){
  $filters=$request->validate(['q'=>['nullable','string','max:120']]);
  $items=GalleryItem::published()->when($filters['q']??null,fn($q,$term)=>$q->where(fn($sub)=>$sub->where('title','like','%'.$term.'%')->orWhere('description','like','%'.$term.'%')))->orderBy('sort_order')->latest()->paginate(18)->withQueryString();
  return view('frontend.gallery.index',compact('items'));
 }
 public function faqs(Request $request){
  $filters=$request->validate(['q'=>['nullable','string','max:120'],'category'=>['nullable','string','max:120']]);
  $faqs=FaqItem::query()->where('is_active',true)->when($filters['q']??null,fn($q,$term)=>$q->where(fn($sub)=>$sub->where('question','like','%'.$term.'%')->orWhere('answer','like','%'.$term.'%')))->when($filters['category']??null,fn($q,$category)=>$q->where('category',$category))->orderBy('sort_order')->orderBy('id')->paginate(20)->withQueryString();
  $categories=FaqItem::query()->where('is_active',true)->whereNotNull('category')->distinct()->orderBy('category')->pluck('category');
  return view('frontend.faq.index',compact('faqs','categories'));
 }
 public function neighborhoods(Request $request){
  $filters=$request->validate(['q'=>['nullable','string','max:120']]);
  $guides=NeighborhoodGuide::published()->when($filters['q']??null,fn($q,$term)=>$q->where(fn($sub)=>$sub->where('title','like','%'.$term.'%')->orWhere('locality','like','%'.$term.'%')->orWhere('excerpt','like','%'.$term.'%')))->orderBy('locality')->paginate(9)->withQueryString();
  return view('frontend.neighborhood-guides.index',compact('guides'));
 }
 public function neighborhood(NeighborhoodGuide $guide){
  abort_unless($guide->status==='published'&&$guide->published_at&&$guide->published_at->lte(now()),404);
  return view('frontend.neighborhood-guides.show',compact('guide'));
 }
}