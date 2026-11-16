<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePropertyRequest;
use App\Models\Category;
use App\Models\Property; use App\Models\PropertyImage;
use Illuminate\Support\Facades\Storage; use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PropertyController extends Controller
{
    private function slug(string $value,?int $ignoreId=null): string
    {
        $base=Str::slug($value)?:Str::random(8);$slug=$base;$i=2;
        while(Property::where('slug',$slug)->when($ignoreId,fn($q)=>$q->whereKeyNot($ignoreId))->exists())$slug=$base.'-'.$i++;
        return $slug;
    }
    public function index(){return view('admin.properties.index',['properties'=>Property::with('category:id,name')->latest()->paginate(15)]);}
    public function create(){return view('admin.properties.form',['property'=>new Property(),'categories'=>Category::where('type','property')->orderBy('name')->get()]);}
    public function store(StorePropertyRequest $request){$data=$request->validated();unset($data['gallery']);$data['slug']=$this->slug($data['slug']??$data['title']);$data['created_by']=$request->user()->id;$data['is_featured']=$request->boolean('is_featured');if($request->hasFile('featured_image'))$data['featured_image']=$request->file('featured_image')->store('properties','public');if($data['status']==='published')$data['published_at']=now();$property=DB::transaction(function()use($data,$request){$created=Property::create($data);foreach($request->file('gallery',[]) as $file)$created->images()->create(['path'=>$file->store('properties/gallery','public')]);return $created;});return redirect()->route('admin.properties.index')->with('success','Property created.');}
    public function edit(Property $property){return view('admin.properties.form',['property'=>$property,'categories'=>Category::where('type','property')->orderBy('name')->get()]);}
    public function update(StorePropertyRequest $request,Property $property){$data=$request->validated();unset($data['gallery']);$data['slug']=$this->slug($data['slug']??$data['title'],$property->id);$data['is_featured']=$request->boolean('is_featured');$oldFeaturedImage=$property->featured_image;if($request->hasFile('featured_image'))$data['featured_image']=$request->file('featured_image')->store('properties','public');if($data['status']==='published'&&!$property->published_at)$data['published_at']=now();DB::transaction(function()use($property,$data,$request){$property->update($data);foreach($request->file('gallery',[]) as $file)$property->images()->create(['path'=>$file->store('properties/gallery','public')]);});if($oldFeaturedImage&&$oldFeaturedImage!==($data['featured_image']??$oldFeaturedImage)&&$property->images()->where('path',$oldFeaturedImage)->doesntExist())Storage::disk('public')->delete($oldFeaturedImage);return redirect()->route('admin.properties.index')->with('success','Property updated.');}
    public function destroy(Property $property){$property->delete();return back()->with('success','Property moved to trash.');}
    public function destroyImage(Property $property,PropertyImage $image){abort_unless($image->property_id===$property->id,404);DB::transaction(function()use($property,$image){if($property->featured_image===$image->path)$property->update(['featured_image'=>null]);$path=$image->path;$image->delete();Storage::disk('public')->delete($path);});return back()->with('success','Gallery image deleted.');}
    public function reorderImages(Request $request,Property $property){$data=$request->validate(['gallery_order'=>['required','array','max:100'],'gallery_order.*'=>['required','integer','distinct',Rule::exists('property_images','id')->where('property_id',$property->id)]]);DB::transaction(function()use($data,$property){foreach($data['gallery_order'] as $order=>$id)$property->images()->whereKey($id)->update(['sort_order'=>$order]);});return back()->with('success','Gallery order saved.');}
    public function setFeaturedImage(Property $property,PropertyImage $image){abort_unless($image->property_id===$property->id,404);$old=$property->featured_image;$property->update(['featured_image'=>$image->path]);if($old&&$old!==$image->path&&$property->images()->where('path',$old)->doesntExist())Storage::disk('public')->delete($old);return back()->with('success','Featured image updated.');}
}