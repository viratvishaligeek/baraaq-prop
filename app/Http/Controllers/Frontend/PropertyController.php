<?php
namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Property;
use Illuminate\Http\Request;
class PropertyController extends Controller
{
 public function index(Request $request)
 {
  $query=Property::published()->with('category:id,name');
  if($request->filled('q'))$query->where('title','like','%'.$request->input('q').'%');
  if($request->filled('city')||$request->filled('location'))$query->where('city',$request->input('city',$request->input('location')));
  if($request->filled('listing_type'))$query->where('listing_type',$request->input('listing_type'));
  if($request->filled('property_type'))$query->where('property_type',$request->input('property_type'));
  if($request->filled('category'))$query->where('category_id',$request->integer('category'));
  if($request->filled('min_price'))$query->where('price','>=',$request->input('min_price'));
  if($request->filled('max_price'))$query->where('price','<=',$request->input('max_price'));
  if($request->filled('max_bedrooms'))$query->where('bedrooms','<=',$request->integer('max_bedrooms'));
  if($request->filled('mini_area'))$query->where('area','>=',$request->input('mini_area'));
  if($request->filled('max_area'))$query->where('area','<=',$request->input('max_area'));
  return view('frontend.properties.index',['properties'=>$query->latest('published_at')->paginate(12)->withQueryString(),'categories'=>Category::where('type','property')->orderBy('name')->get()]);
 }
 public function show(Property $property){abort_unless($property->status==='published'&&$property->published_at&&$property->published_at->lte(now()),404);$property->load(['category','images']);$related=Property::published()->where('id','!=',$property->id)->where('city',$property->city)->limit(3)->get();return view('frontend.properties.show',compact('property','related'));}
}