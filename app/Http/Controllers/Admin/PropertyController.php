<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller; use App\Http\Requests\StorePropertyRequest; use App\Models\Category; use App\Models\Property; use Illuminate\Support\Str; use Illuminate\Support\Facades\Storage;
class PropertyController extends Controller {
 public function index(){return view('admin.properties.index',['properties'=>Property::with('category:id,name')->latest()->paginate(15)]);}
 public function create(){return view('admin.properties.form',['property'=>new Property(),'categories'=>Category::where('type','property')->orderBy('name')->get()]);}
 public function store(StorePropertyRequest $r){$d=$r->validated();$d['slug']=$d['slug']??Str::slug($d['title']);$d['created_by']=$r->user()->id;if($r->hasFile('featured_image'))$d['featured_image']=$r->file('featured_image')->store('properties','public');if($d['status']==='published')$d['published_at']=now();Property::create($d);return redirect()->route('admin.properties.index')->with('success','Property created.');}
 public function edit(Property $property){return view('admin.properties.form',['property'=>$property,'categories'=>Category::where('type','property')->orderBy('name')->get()]);}
 public function update(StorePropertyRequest $r,Property $property){$d=$r->validated();$d['slug']=$d['slug']??Str::slug($d['title']);if($r->hasFile('featured_image')){if($property->featured_image)Storage::disk('public')->delete($property->featured_image);$d['featured_image']=$r->file('featured_image')->store('properties','public');}if($d['status']==='published'&&!$property->published_at)$d['published_at']=now();$property->update($d);return redirect()->route('admin.properties.index')->with('success','Property updated.');}
 public function destroy(Property $property){$property->delete();return back()->with('success','Property moved to trash.');}
}