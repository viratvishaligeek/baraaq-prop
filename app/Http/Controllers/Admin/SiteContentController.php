<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\FaqItem;
use App\Models\GalleryItem;
use App\Models\Media;
use App\Models\NeighborhoodGuide;
use App\Support\MediaReferenceChecker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
class SiteContentController extends Controller {
 private array $models=['gallery'=>GalleryItem::class,'faqs'=>FaqItem::class,'neighborhood-guides'=>NeighborhoodGuide::class];
 private function model(string $type): string {
  abort_unless(isset($this->models[$type]),404);
  $user=request()->user();
  $action=match(true){request()->routeIs('admin.site-content.create','admin.site-content.store')=>'create',request()->routeIs('admin.site-content.edit','admin.site-content.update')=>'edit',request()->routeIs('admin.site-content.destroy')=>'delete',default=>'view'};
  abort_unless($user->isAdministrator()||$user->hasPermission($type.'.manage')||$user->hasPermission($type.'.'.$action),403);
  return $this->models[$type];
 }
 public function index(string $type) {
  $model=$this->model($type);
  $filters=request()->validate(['q'=>['nullable','string','max:160'],'status'=>['nullable',Rule::in(['draft','published','archived'])]]);
  $items=$model::query()->when($filters['q']??null,function($q,$term)use($type){$cols=$type==='faqs'?['question','answer','category']:['title','slug'];$q->where(function($sub)use($cols,$term){foreach($cols as $i=>$col)$sub->{$i?'orWhere':'where'}($col,'like','%'.trim($term).'%');});})->when(isset($filters['status'])&&$type!=='faqs',fn($q)=>$q->where('status',$filters['status']))->orderBy($type==='faqs'?'sort_order':'updated_at',$type==='faqs'?'asc':'desc')->paginate(25)->withQueryString();
  return view('admin.site-content.index',compact('type','items'));
 }
 public function create(string $type){$model=$this->model($type);return view('admin.site-content.form',['type'=>$type,'item'=>new $model()]);}
 public function store(Request $request,string $type){
  $model=$this->model($type);$data=$this->validated($request,$type);$this->authorizePublish($type,$data);
  $path=null;
  try{
   if($type!=='faqs'&&$request->hasFile('image')){$path=$request->file('image')->store($type,'public');if(!$path)throw new \RuntimeException('Image storage failed.');$data[$type==='gallery'?'image':'hero_image']=$path;}
   if($type!=='faqs'){$data['slug']=$this->uniqueSlug($model,$data['slug']??$data['title']);if($data['status']==='published')$data['published_at']=now();}
   if($type==='faqs')$data['is_active']=$request->boolean('is_active');
   DB::transaction(function()use($model,$data,$type,$request){$item=$model::create($data);if($type!=='faqs'&&$request->hasFile('image'))$this->registerMedia($request,$data[$type==='gallery'?'image':'hero_image'],$request->file('image'),$data['alt_text']??$data['title']);});
  }catch(\Throwable $e){if($path)Storage::disk('public')->delete($path);throw $e;}
  return redirect()->route('admin.site-content.index',$type)->with('success','Content created.');
 }
 public function edit(string $type,int $id){$model=$this->model($type);return view('admin.site-content.form',['type'=>$type,'item'=>$model::findOrFail($id)]);}
 public function update(Request $request,string $type,int $id){
  $model=$this->model($type);$item=$model::findOrFail($id);$data=$this->validated($request,$type,$id);$this->authorizePublish($type,$data,$item);
  $field=$type==='gallery'?'image':'hero_image';$oldPath=$type==='faqs'?null:$item->$field;$newPath=null;
  try{
   if($type!=='faqs'&&$request->hasFile('image')){$newPath=$request->file('image')->store($type,'public');if(!$newPath)throw new \RuntimeException('Image storage failed.');$data[$field]=$newPath;}
   elseif($type!=='faqs'&&$request->boolean('remove_image'))$data[$field]=null;
   if($type!=='faqs'&&($data['status']??null)==='published'&&!$item->published_at)$data['published_at']=now();
   if($type==='faqs')$data['is_active']=$request->boolean('is_active');
   DB::transaction(function()use($item,$data,$type,$request,$newPath){$item->update($data);if($newPath)$this->registerMedia($request,$newPath,$request->file('image'),$data['alt_text']??$data['title']);});
  }catch(\Throwable $e){if($newPath)Storage::disk('public')->delete($newPath);throw $e;}
  if($oldPath&&$oldPath!==($item->$field)&&!app(MediaReferenceChecker::class)->isPathReferenced($oldPath))Storage::disk('public')->delete($oldPath);
  return redirect()->route('admin.site-content.index',$type)->with('success','Content updated.');
 }
 public function destroy(string $type,int $id){
  $model=$this->model($type);$item=$model::findOrFail($id);$path=$type==='gallery'?$item->image:($type==='neighborhood-guides'?$item->hero_image:null);$item->delete();
  if($path&&!app(MediaReferenceChecker::class)->isPathReferenced($path))Storage::disk('public')->delete($path);
  return back()->with('success','Content deleted.');
 }
 private function validated(Request $request,string $type,?int $id=null):array{
  if($type==='faqs')return $request->validate(['question'=>['required','string','max:255'],'answer'=>['required','string','max:12000'],'category'=>['nullable','string','max:120'],'sort_order'=>['nullable','integer','min:0'],'is_active'=>['sometimes','boolean']]);
  $table=$type==='gallery'?'gallery_items':'neighborhood_guides';
  return $request->validate(['title'=>['required','string','max:255'],'slug'=>['nullable','string','max:255',Rule::unique($table)->ignore($id)],'description'=>['nullable','string','max:3000'],'locality'=>[$type==='neighborhood-guides'?'required':'nullable','string','max:160'],'city'=>['nullable','string','max:100'],'state'=>['nullable','string','max:100'],'excerpt'=>['nullable','string','max:500'],'content'=>['nullable','string'],'image'=>[$type==='gallery'&&!$id?'required':'nullable','image','mimes:jpg,jpeg,png,webp','max:5120'],'alt_text'=>['nullable','string','max:255'],'remove_image'=>['sometimes','boolean'],'sort_order'=>['nullable','integer','min:0'],'status'=>['required','in:draft,published,archived'],'meta_title'=>['nullable','string','max:255'],'meta_description'=>['nullable','string','max:1000'],'canonical_url'=>['nullable','url','max:2048'],'og_image'=>['nullable','string','max:2048'],'focus_keyword'=>['nullable','string','max:160']]);
 }
 private function uniqueSlug(string $model,string $value):string{$base=Str::slug($value)?:Str::random(8);$slug=$base;$n=2;while($model::withTrashed()->where('slug',$slug)->exists())$slug=$base.'-'.$n++;return $slug;}
 private function authorizePublish(string $type,array $data,$item=null):void{if($type!=='faqs'&&($data['status']??null)==='published'&&$item?->status!=='published'){$u=request()->user();abort_unless($u->isAdministrator()||$u->hasPermission($type.'.manage')||$u->hasPermission($type.'.publish'),403);}}
 private function registerMedia(Request $request,string $path,$file,string $alt):void{Media::create(['uploaded_by'=>$request->user()->id,'disk'=>'public','path'=>$path,'original_name'=>$file->getClientOriginalName(),'mime_type'=>$file->getMimeType(),'size'=>$file->getSize(),'alt_text'=>$alt]);}
}