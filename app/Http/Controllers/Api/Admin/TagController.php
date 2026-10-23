<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::withCount('posts')->latest()->get();

        return ApiResponse::success($tags, 'Tags retrieved successfully.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tags,name',
            'is_active' => 'sometimes|boolean',
        ]);

        $tag = Tag::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'is_active' => $request->input('is_active', true),
        ]);

        return ApiResponse::success($tag, 'Tag created successfully.', 201);
    }

    public function show($id)
    {
        $tag = Tag::withCount('posts')->findOrFail($id);

        return ApiResponse::success($tag, 'Tag retrieved successfully.');
    }

    public function update(Request $request, $id)
    {
        $tag = Tag::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|required|string|max:255|unique:tags,name,' . $tag->id,
            'is_active' => 'sometimes|boolean',
        ]);

        $data = $request->only(['name', 'is_active']);
        
        if ($request->has('name')) {
            $data['slug'] = Str::slug($request->name);
        }

        $tag->update($data);

        return ApiResponse::success($tag, 'Tag updated successfully.');
    }

    public function toggleStatus($id)
    {
        $tag = Tag::findOrFail($id);
        $tag->update(['is_active' => !$tag->is_active]);

        $message = $tag->is_active 
            ? 'Tag activated successfully.' 
            : 'Tag deactivated successfully.';

        return ApiResponse::success($tag, $message);
    }

    public function destroy($id)
    {
        $tag = Tag::findOrFail($id);
        $tag->delete();

        return ApiResponse::success(null, 'Tag deleted successfully.');
    }
}