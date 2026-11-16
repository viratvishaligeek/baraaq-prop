<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePropertyRequest;
use App\Models\Category;
use App\Models\Property;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
    public function store(StorePropertyRequest $request){$data=$request->validated();$data['slug']=$this->slug($data['slug']??$data['title']);$data['created_by']=$request->user()->id;if($request->hasFile('featured_image'))$data['featured_image']=$request->file('featured_image')->store('properties','public');if($data['status']==='published')$data['published_at']=now();Property::create($data);return redirect()->route('admin.properties.index')->with('success','Property created.');}
    public function edit(Property $property){return view('admin.properties.form',['property'=>$property,'categories'=>Category::where('type','property')->orderBy('name')->get()]);}
    public function update(StorePropertyRequest $request,Property $property){$data=$request->validated();$data['slug']=$this->slug($data['slug']??$data['title'],$property->id);if($request->hasFile('featured_image')){if($property->featured_image)Storage::disk('public')->delete($property->featured_image);$data['featured_image']=$request->file('featured_image')->store('properties','public');}if($data['status']==='published'&&!$property->published_at)$data['published_at']=now();$property->update($data);return redirect()->route('admin.properties.index')->with('success','Property updated.');}
    public function destroy(Property $property){$property->delete();return back()->with('success','Property moved to trash.');}
}