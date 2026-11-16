<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller; use App\Models\Menu; use Illuminate\Http\Request; use Illuminate\Support\Facades\DB; use Illuminate\Validation\Rule;
class MenuController extends Controller {
 public function index(){return view('admin.menus.index',['menus'=>Menu::withCount('items')->orderBy('name')->get()]);}
 public function create(){return view('admin.menus.form',['menu'=>new Menu]);}
 public function store(Request $r){$d=$r->validate(['name'=>['required','string','max:120'],'location'=>['required','string','max:80','alpha_dash','unique:menus,location'],'items'=>['nullable','array'],'items.*.label'=>['required_with:items','string','max:120'],'items.*.url'=>['nullable','string','max:2048'],'items.*.target'=>['nullable',Rule::in(['_self','_blank'])]]);$menu=DB::transaction(function()use($d){$m=Menu::create(['name'=>$d['name'],'location'=>$d['location']]);$this->syncItems($m,$d['items']??[]);return $m;});return redirect()->route('admin.menus.edit',$menu)->with('success','Menu created.');}
 public function edit(Menu $menu){$menu->load('items');return view('admin.menus.form',compact('menu'));}
 public function update(Request $r,Menu $menu){$d=$r->validate(['name'=>['required','string','max:120'],'location'=>['required','string','max:80','alpha_dash',Rule::unique('menus')->ignore($menu->id)],'items'=>['nullable','array'],'items.*.label'=>['required_with:items','string','max:120'],'items.*.url'=>['nullable','string','max:2048'],'items.*.target'=>['nullable',Rule::in(['_self','_blank'])]]);DB::transaction(function()use($menu,$d){$menu->update(['name'=>$d['name'],'location'=>$d['location']]);$menu->items()->delete();$this->syncItems($menu,$d['items']??[]);});return back()->with('success','Menu updated.');}
 public function destroy(Menu $menu){$menu->delete();return back()->with('success','Menu deleted.');}
 private function syncItems(Menu $menu,array $items):void{foreach(array_values($items) as $i=>$item){if(trim($item['label']??'')==='')continue;$menu->items()->create(['label'=>$item['label'],'url'=>$item['url']??null,'target'=>$item['target']??'_self','sort_order'=>$i,'is_active'=>true]);}}
}