<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ContentController extends Controller
{
    private array $types = ['posts'=>Post::class,'pages'=>Page::class,'categories'=>Category::class,'banners'=>Banner::class,'team'=>TeamMember::class,'services'=>Service::class,'testimonials'=>Testimonial::class];

    private function model(string $type): string
    {
        abort_unless(isset($this->types[$type]),404);
        $permission = ['posts'=>'posts.manage','pages'=>'pages.manage','categories'=>'categories.manage','banners'=>'banners.manage','team'=>'team.manage','services'=>'services.manage','testimonials'=>'testimonials.manage'][$type];
        abort_unless(request()->user()->isAdministrator() || request()->user()->hasPermission($permission),403);
        return $this->types[$type];
    }

    private function uniqueSlug(string $model,string $value,?int $ignoreId=null): string
    {
        $base = Str::slug($value) ?: Str::random(8);
        $slug = $base;
        $suffix = 2;
        while ($model::where('slug',$slug)->when($ignoreId,fn($q)=>$q->whereKeyNot($ignoreId))->exists()) $slug = $base.'-'.$suffix++;
        return $slug;
    }

    public function index(string $type)
    {
        $model=$this->model($type);
        $items=$model::when(request()->filled('search'),function($query){$term=request('search');$query->where(function($where)use($term){foreach(['title','name','slug','email','designation','company'] as $column){if(\\Illuminate\\Support\\Schema::hasColumn($where->getModel()->getTable(),$column))$where->orWhere($column,'like','%'.$term.'%');}});})->when(request()->filled('status') && in_array($type,['posts','pages','services']),fn($q)=>$q->where('status',request('status')))->latest()->paginate(20)->withQueryString();
        return view('admin.content.index',compact('type','items'));
    }

    public function create(string $type)
    {
        $model=$this->model($type);
        return view('admin.content.form',['type'=>$type,'item'=>new $model,'categories'=>$type==='posts'?Category::where('type','post')->orderBy('name')->get():collect(),'parentCategories'=>$type==='categories'?Category::orderBy('name')->get():collect()]);
    }

    public function store(Request $request,string $type)
    {
        $model=$this->model($type);
        $data=$this->validated($request,$type);$categoryIds=$data['category_ids']??[];unset($data['category_ids']);
        if(isset($data['title'])) $data['slug']=$this->uniqueSlug($model,$data['slug']??$data['title']);
        if(isset($data['name'])) $data['slug']=$this->uniqueSlug($model,$data['slug']??$data['name']);
        if(in_array($type,['posts','pages'])) $data['author_id']=$request->user()->id;
        foreach(['image','featured_image','photo','icon_path','og_image'] as $field) if($request->hasFile($field)) $data[$field]=$request->file($field)->store($type,'public');
        if(in_array($type,['posts','services']) && ($data['status']??null)==='published') $data['published_at']=now();
        if(in_array($type,['banners','team','categories','testimonials'])) $data['is_active']=$request->boolean('is_active');
        if($type==='team' && isset($data['social_links'])) { $links=json_decode($data['social_links'],true); $data['social_links']=is_array($links)?$links:[]; }
        $item=DB::transaction(function()use($model,$data,$type,$categoryIds){$created=$model::create($data);if($type==='posts')$created->categories()->sync($categoryIds);return $created;});
        return redirect()->route('admin.content.index',$type)->with('success',ucfirst($type).' item created.');
    }

    public function edit(string $type,int $id)
    {
        $model=$this->model($type);
        $item=$model::findOrFail($id);
        return view('admin.content.form',['type'=>$type,'item'=>$item,'categories'=>$type==='posts'?Category::where('type','post')->orderBy('name')->get():collect(),'parentCategories'=>$type==='categories'?Category::where('type',$item->type)->where('id','!=',$item->id)->orderBy('name')->get():collect()]);
    }

    public function update(Request $request,string $type,int $id)
    {
        $model=$this->model($type);
        $item=$model::findOrFail($id);
        $data=$this->validated($request,$type,$item->id);$categoryIds=$data['category_ids']??[];unset($data['category_ids']);
        if(isset($data['title'])) $data['slug']=$this->uniqueSlug($model,$data['slug']??$data['title'],$item->id);
        if(isset($data['name'])) $data['slug']=$this->uniqueSlug($model,$data['slug']??$data['name'],$item->id);
        $oldFiles=[];
        foreach(['image','featured_image','photo','icon_path'] as $field) if($request->hasFile($field)) {
            if($item->$field) $oldFiles[]=$item->$field;
            $data[$field]=$request->file($field)->store($type,'public');
        }
        if(in_array($type,['posts','services']) && ($data['status']??null)==='published' && !$item->published_at) $data['published_at']=now();
        if(in_array($type,['banners','team','categories','testimonials'])) $data['is_active']=$request->boolean('is_active');
        if($type==='team' && isset($data['social_links'])) { $links=json_decode($data['social_links'],true); $data['social_links']=is_array($links)?$links:[]; }
        DB::transaction(function()use($item,$data,$type,$categoryIds){$item->update($data);if($type==='posts')$item->categories()->sync($categoryIds);});
        foreach($oldFiles as $path)Storage::disk('public')->delete($path);
        return redirect()->route('admin.content.index',$type)->with('success',ucfirst($type).' item updated.');
    }

    public function destroy(string $type,int $id)
    {
        $model=$this->model($type);
        $model::findOrFail($id)->delete();
        return back()->with('success','Item moved to trash.');
    }

    private function validated(Request $request,string $type,?int $id=null): array
    {
        $rules=match($type) {
            'posts'=>['title'=>['required','string','max:255'],'slug'=>['nullable','string','max:255',Rule::unique('posts')->ignore($id)],'excerpt'=>['nullable','string'],'content'=>['nullable','string'],'category_ids'=>['nullable','array'],'category_ids.*'=>['integer',Rule::exists('categories','id')->where('type','post')],'featured_image'=>['nullable','image','mimes:jpg,jpeg,png,webp','max:5120'],'status'=>['required','in:draft,published,archived'],'meta_title'=>['nullable','string','max:255'],'meta_description'=>['nullable','string','max:1000']],
            'pages'=>['title'=>['required','string','max:255'],'slug'=>['nullable','string','max:255',Rule::unique('pages')->ignore($id)],'content'=>['nullable','string'],'status'=>['required','in:draft,published,archived'],'meta_title'=>['nullable','string','max:255'],'meta_description'=>['nullable','string','max:1000']],
            'categories'=>['name'=>['required','string','max:150'],'slug'=>['nullable','string','max:180',Rule::unique('categories')->ignore($id)],'description'=>['nullable','string'],'icon_path'=>['nullable','image','mimes:png,jpg,jpeg,webp','max:2048'],'sort_order'=>['nullable','integer','min:0'],'is_active'=>['sometimes','boolean'],'parent_id'=>['nullable',Rule::exists('categories','id')->where(fn($q)=>$q->where('type',$request->input('type'))->when($id,fn($q)=>$q->where('id','!=',$id)))],'type'=>['required','in:property,post']],
            'banners'=>['title'=>['required','string','max:255'],'subtitle'=>['nullable','string','max:255'],'description'=>['nullable','string'],'image'=>['nullable','image','mimes:jpg,jpeg,png,webp','max:5120'],'button_text'=>['nullable','string','max:100'],'button_url'=>['nullable','string','max:2048','regex:/^(https?:\/\/|\/|#)/i'],'placement'=>['required','string','max:60'],'sort_order'=>['nullable','integer','min:0'],'starts_at'=>['nullable','date'],'ends_at'=>['nullable','date','after_or_equal:starts_at'],'is_active'=>['sometimes','boolean']],
            'team'=>['name'=>['required','string','max:150'],'designation'=>['nullable','string','max:150'],'bio'=>['nullable','string'],'photo'=>['nullable','image','mimes:jpg,jpeg,png,webp','max:5120'],'email'=>['nullable','email'],'phone'=>['nullable','string','max:30'],'social_links'=>['nullable','json'],'sort_order'=>['nullable','integer','min:0'],'is_active'=>['sometimes','boolean']],
            'services'=>['title'=>['required','string','max:255'],'slug'=>['nullable','string','max:255',Rule::unique('services')->ignore($id)],'excerpt'=>['nullable','string','max:500'],'content'=>['nullable','string'],'featured_image'=>['nullable','image','mimes:jpg,jpeg,png,webp','max:5120'],'status'=>['required','in:draft,published,archived'],'meta_title'=>['nullable','string','max:255'],'meta_description'=>['nullable','string','max:1000'],'canonical_url'=>['nullable','url','max:2048'],'og_image'=>['nullable','image','mimes:jpg,jpeg,png,webp','max:5120']],
            'testimonials'=>['name'=>['required','string','max:150'],'designation'=>['nullable','string','max:150'],'company'=>['nullable','string','max:150'],'quote'=>['required','string'],'photo'=>['nullable','image','mimes:jpg,jpeg,png,webp','max:5120'],'rating'=>['nullable','integer','between:1,5'],'sort_order'=>['nullable','integer','min:0'],'is_active'=>['sometimes','boolean']]
        };
        return $request->validate($rules);
    }
}