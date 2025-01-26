<?php

namespace Invento\Blog\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Invento\Blog\Models\Blog;
use Invento\Blog\Models\Category;
use Invento\Blog\Requests\BlogRequest;
use Invento\Blog\Services\BlogService;
use Spatie\Tags\Tag;
use App\Models\TagManager;
use Brian2694\Toastr\Facades\Toastr;

class BlogController extends Controller
{

    public function __construct()
    {
        $this->middleware(['permission:view blogs'])->only(['index']);
        $this->middleware(['permission:add and update blog'])->only(['create','store','edit','update']);
        $this->middleware(['permission:delete blog'])->only(['destroy']);
    }
    
    public function index()
    {
        $data['blogs'] = Blog::query()
            ->search(request()->input('query'))
            ->orderByDesc('created_at')
            ->paginate(10);

        return view("blog::blogs.index",$data);
    }

    public function create()
    {
        $data['categories'] = Category::active()->pluck('name','id')->toArray();
        $data['tags'] = Tag::where('type', TagManager::TYPE['Blog'])->pluck('name', 'id');
        $data['blog'] = new Blog();

        return view('blog::blogs.create',$data);
    }

    public function store(BlogRequest $request)
    {
        $category = Category::where('id',$request->category)
            ->active()
            ->first();

        if(!$category){
            Toastr::success(__('blog::categories.blog_category_not_found'),__('blog::categories.blog_category'));
            return back()->withInput();
        }
        $response = BlogService::store($request,$category);

        return $response ?  redirect()->route('admin.blogs.index') : back()->withInput();
    }

    public function show($id)
    {
        //
    }

    public function edit(Blog $blog)
    {
        $data['blog'] = $blog->load('tags');
        $data['selected_tags'] = $blog->tags->pluck('id')->toArray();
        $data['categories'] = Category::active()->pluck('name','id')->toArray();
        $data['tags'] = Tag::where('type', TagManager::TYPE['Blog'])->pluck('name', 'id');

        return view('blog::blogs.edit',$data);
    }



    public function update(Request $request, Blog $blog)
    {
        if($request->has('status_switch')){
            $blog->update([
                'status' => !$blog->status,
            ]);

            Toastr::success(__('blog::blogs.blog_updated_successfully'),__('blog::blogs.blog'));

            return back();
        }else{
            $category = Category::where('id',$request->category)
                ->active()
                ->first();

            if(!$category){
                Toastr::success(__('blog::categories.blog_category_not_found'),__('blog::categories.blog_category'));
                return back()->withInput();
            }
            $response = BlogService::update($request,$blog,$category);
            return $response ?  back() : back()->withInput();
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Blog $blog
     * @return Response
     */
    public function destroy(Blog $blog)
    {
        $blog->delete();
        Toastr::success(__('blog::blogs.blog_deleted_successfully'),__('blog::blogs.blog'));
        return back();
    }

}
