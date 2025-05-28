<?php

namespace Invento\Blog\Controllers;

use Invento\Blog\Models\Blog;
use Invento\Blog\Models\Category;
use Invento\Blog\Resource\CategoryResource;
use Invento\Blog\Resource\BlogResource;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller; // Make sure to use the base Controller

class BlogApiController extends Controller
{
    public function index()
    {
        // Eager load category to prevent N+1 issues if you access it in the resource
        $blogs = Blog::with('category')->where('status', true)->orderBy('display_order')->paginate(10);
 
        $blogs->through(function ($blog) {
            return new BlogResource($blog);
        });


        return ApiResponse::successWithPagination(
            $blogs,
            'Blog Posts retrieved successfully.',
            200
        );
    }

    public function show($id)
    {
        $blog = Blog::with('category')->where('id', $id)->where('status', true)->first();

        if($blog){
            return ApiResponse::success(
                new BlogResource($blog),
                'Blog Post retrieved successfully.',
                200
            );
        }else{
            return ApiResponse::notFound(
                'Blog Post not found.'
            );
        }
    }

    public function categories()
    {
        $categories = Category::where('status', true)->orderBy('display_order')->get();
        return ApiResponse::success(
            CategoryResource::collection($categories),
            'Blog Categories retrieved successfully.',
            200
        );;

    }
}