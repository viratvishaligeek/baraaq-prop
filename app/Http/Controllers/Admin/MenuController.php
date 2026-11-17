<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Page;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
class MenuController extends Controller
{
 public function index(){return view('admin.menus.index',['menus'=>Menu::withCount('allItems')->orderBy('name')->paginate(20)]);}
 public function create(){return view('admin.menus.form',$this->formData(new Menu()));}
 public function store(Request $r){$d=$this->validated($r);$m=DB::transaction(function()use($d){$m=Menu::create(['name'=>$d['name'],'location'=>$d['location']]);$this->syncItems($m,$d['items']??[]);return $m;});Cache::forget('menu:'.$m->location);return redirect()->route('admin.menus.edit',$m)->with('success','Menu created.');}
 public function edit(Menu $menu){$items=$menu->allItems()->orderBy('sort_order')->get();$indexes=$items->pluck('id')->flip();$items->each(function($item)use($indexes){$item->parent_index=$item->parent_id!==null?($indexes[$item->parent_id]??null):null;});return view('admin.menus.form',$this->formData($menu,$items));}
 public function update(Request $r,Menu $menu){$d=$this->validated($r,$menu);$old=$menu->location;DB::transaction(function()use($menu,$d){$menu->update(['name'=>$d['name'],'location'=>$d['location']]);$menu->allItems()->delete();$this->syncItems($menu,$d['items']??[]);});Cache::forget('menu:'.$old);Cache::forget('menu:'.$menu->location);return back()->with('success','Menu updated.');}
 public function destroy(Menu $menu){$location=$menu->location;$menu->delete();Cache::forget('menu:'.$location);return back()->with('success','Menu deleted.');}
 private function validated(Request $r,?Menu $menu=null):array{return $r->validate(['name'=>['required','string','max:120'],'location'=>['required','string','max:80','alpha_dash',Rule::unique('menus','location')->ignore($menu?->id)],'items'=>['nullable','array','max:50'],'items.*.label'=>['required','string','max:120'],'items.*.link_type'=>['nullable',Rule::in(['custom','page','blog_category','property_category','service'])],'items.*.link_id'=>['nullable','integer'],'items.*.url'=>['nullable','string','max:2048','regex:/^(https?:\/\/|\/|mailto:|tel:|#)/i'],'items.*.target'=>['required',Rule::in(['_self','_blank'])],'items.*.parent_index'=>['nullable','integer','min:0','max:49'],'items.*.is_active'=>['nullable','boolean']]);}
 private function formData(Menu $menu, $items = null): array\n {\n  return ['menu'=>$menu,'menuItems'=>$items ?? collect(),'pages'=>Page::where('status','published')->orderBy('title')->get(['id','title']),'blogCategories'=>Category::where('type','post')->orderBy('name')->get(['id','name']),'propertyCategories'=>Category::where('type','property')->orderBy('name')->get(['id','name'])];\n }\n private function syncItems(Menu $menu,array $items):void
 {
  $created=[];$depths=[];
  foreach(array_values($items) as $index=>$item){
   $parent=$item['parent_index']??null;
   if($parent!==null&&(!isset($created[(int)$parent])||(int)$parent>=$index))throw \Illuminate\Validation\ValidationException::withMessages(['items.'.$index.'.parent_index'=>'Choose a parent item positioned above this item.']);
   $depth=$parent!==null?$depths[(int)$parent]+1:0;
   if($depth>4)throw \Illuminate\Validation\ValidationException::withMessages(['items.'.$index.'.parent_index'=>'Menus support up to five levels of nesting.']);
   $created[$index]=$menu->allItems()->create(['label'=>trim($item['label']),'url'=>trim($item['url']??'')?:null,'target'=>$item['target']??'_self','parent_id'=>$parent!==null?$created[(int)$parent]->id:null,'sort_order'=>$index,'is_active'=>isset($item['is_active'])]);
   $depths[$index]=$depth;
  }
 }
}