<?php

namespace Invento\Blog\Controllers;

use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application as ApplicationAlias1;
use Illuminate\Contracts\View\Factory as FactoryAlias;
use Illuminate\Contracts\View\View as ViewAlias;
use Illuminate\Foundation\Application as ApplicationAlias;
use Illuminate\Http\Request;
use Invento\Blog\Models\Category;
use Invento\Blog\Requests\CategoryRequest;
use App\Services\CustomFieldService;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:view blog categories'])->only(['index']);
        $this->middleware(['permission:add and update blog category'])->only(['create','store','edit','update']);
        $this->middleware(['permission:delete blog category'])->only(['destroy']);
    }

    public function index()
    {
        $data['categories'] = Category::query()
            ->search(request()->input('query'))
            ->orderByDesc('id')
            ->paginate(10);

        return view("blog::categories.index",$data);
    }

    public function create(){
        $data['category'] = new Category();
        return view("blog::categories.create",$data);
    }

    public function store(CategoryRequest $request){
        $category = Category::create([
           'name' => $request->name,
           'display_order' => $request->display_order,
            'status' => $request->has('status'),
            'meta_title' => $request->meta_title ?? $request->name,
            'meta_description' => $request->meta_description ?? '',
        ]);

        CustomFieldService::add($request->custom_fields,$category,\App\Models\CustomField::MODULES['Blog Category']);

        Toastr::success(__('blog::categories.blog_category_added'),__('blog::categories.blog_category'));

        return redirect()->route('admin.blogs.categories.index');
    }

    public function edit(Category $category){
        $data['category'] = $category;
        return view("blog::categories.edit",$data);
    }

    public function update(Request $request,Category $category){

        if($request->has('status_switch')){
            $category->update([
                'status' => !$category->status,
            ]);
        }else{
            $category->update([
                'name' => $request->name,
                'display_order' => $request->display_order,
                'status' => $request->has('status'),
                'meta_title' => $request->meta_title ?? $request->name,
                'meta_description' => $request->meta_description ?? '',
            ]);

            CustomFieldService::add($request->custom_fields,$category,\App\Models\CustomField::MODULES['Blog Category']);
        }

        Toastr::success(__('blog::categories.blog_category_updated'),__('blog::categories.blog_category'));

        return back();
    }

    public function destroy(Category $category){
        $category->delete();
        Toastr::success(__('blog::categories.blog_category_deleted'),__('blog::categories.blog_category'));

        return back();
    }
}
