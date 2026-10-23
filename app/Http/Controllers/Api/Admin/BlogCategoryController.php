<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogCategoryController extends Controller
{
    public function index()
    {
        $categories = BlogCategory::withCount('posts')->latest()->get();

        return ApiResponse::success($categories, 'Blog categories retrieved successfully.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:blog_categories,name',
            'is_active' => 'sometimes|boolean',
        ]);

        $category = BlogCategory::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'is_active' => $request->input('is_active', true),
        ]);

        return ApiResponse::success($category, 'Blog category created successfully.', 201);
    }

    public function show($id)
    {
        $category = BlogCategory::withCount('posts')->findOrFail($id);

        return ApiResponse::success($category, 'Blog category retrieved successfully.');
    }

    public function update(Request $request, $id)
    {
        $category = BlogCategory::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|required|string|max:255|unique:blog_categories,name,' . $category->id,
            'is_active' => 'sometimes|boolean',
        ]);

        $data = $request->only(['name', 'is_active']);
        
        if ($request->has('name')) {
            $data['slug'] = Str::slug($request->name);
        }

        $category->update($data);

        return ApiResponse::success($category, 'Blog category updated successfully.');
    }

    public function toggleStatus($id)
    {
        $category = BlogCategory::findOrFail($id);
        $category->update(['is_active' => !$category->is_active]);

        $message = $category->is_active 
            ? 'Blog category activated successfully.' 
            : 'Blog category deactivated successfully.';

        return ApiResponse::success($category, $message);
    }

    public function destroy($id)
    {
        $category = BlogCategory::findOrFail($id);
        $category->delete();

        return ApiResponse::success(null, 'Blog category deleted successfully.');
    }
}