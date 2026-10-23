<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('menuItems')->latest()->get();

        return ApiResponse::success($categories, 'Categories retrieved successfully.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:1024',
            'is_active' => 'sometimes|boolean',
        ]);

        $data = [
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'is_active' => $request->input('is_active', true),
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories/images', 'public');
        }

        if ($request->hasFile('icon')) {
            $data['icon'] = $request->file('icon')->store('categories/icons', 'public');
        }

        $category = Category::create($data);

        return ApiResponse::success($category, 'Category created successfully.', 201);
    }

    public function show($id)
    {
        $category = Category::with('menuItems')->findOrFail($id);

        return ApiResponse::success($category, 'Category retrieved successfully.');
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|required|string|max:255|unique:categories,name,' . $category->id,
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,webp|max:2048',
            'icon' => 'sometimes|image|mimes:jpeg,png,jpg,webp,svg|max:1024',
            'is_active' => 'sometimes|boolean',
        ]);

        $data = $request->only(['name', 'is_active']);

        if ($request->has('name')) {
            $data['slug'] = Str::slug($request->name);
        }

        if ($request->hasFile('image')) {
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }
            $data['image'] = $request->file('image')->store('categories/images', 'public');
        }

        if ($request->hasFile('icon')) {
            if ($category->icon && Storage::disk('public')->exists($category->icon)) {
                Storage::disk('public')->delete($category->icon);
            }
            $data['icon'] = $request->file('icon')->store('categories/icons', 'public');
        }

        $category->update($data);

        return ApiResponse::success($category, 'Category updated successfully.');
    }

    public function toggleStatus($id)
    {
        $category = Category::findOrFail($id);
        $category->update(['is_active' => !$category->is_active]);

        $message = $category->is_active 
            ? 'Category activated successfully.' 
            : 'Category deactivated successfully.';

        return ApiResponse::success($category, $message);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        if ($category->image && Storage::disk('public')->exists($category->image)) {
            Storage::disk('public')->delete($category->image);
        }

        if ($category->icon && Storage::disk('public')->exists($category->icon)) {
            Storage::disk('public')->delete($category->icon);
        }

        $category->delete();

        return ApiResponse::success(null, 'Category deleted successfully.');
    }
}