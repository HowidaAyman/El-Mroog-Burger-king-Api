<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Traits\PaginationTrait;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostController extends Controller
{
    use PaginationTrait;

    public function index(Request $request)
    {
        $query = Post::with(['user:id,name', 'category:id,name', 'tags:id,name']);

        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('blog_category_id', $request->category_id);
        }

        if ($request->has('search') && $request->search != '') {
            $query->where('title', 'like', "%{$request->search}%");
        }

        $posts = $query->latest()->paginate($this->getPerPage($request));

        return ApiResponse::paginated($posts, 'Posts retrieved successfully.');
    }
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'blog_category_id' => 'required|exists:blog_categories,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'is_active' => 'sometimes|boolean',
        ]);

        $data = [
            'user_id' => $request->user()->id,
            'blog_category_id' => $request->blog_category_id,
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . time(),
            'content' => $request->content,
            'is_active' => $request->input('is_active', true),
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('posts', 'public');
        }

        $post = Post::create($data);

        if ($request->has('tags')) {
            $post->tags()->sync($request->tags);
        }

        return ApiResponse::success(
            $post->load(['user:id,name', 'category:id,name', 'tags:id,name']),
            'Post created successfully.',
            201
        );
    }

    public function show($id)
    {
        $post = Post::with(['user:id,name', 'category:id,name', 'tags:id,name', 'comments'])
            ->findOrFail($id);

        return ApiResponse::success($post, 'Post retrieved successfully.');
    }

    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'blog_category_id' => 'sometimes|required|exists:blog_categories,id',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,webp|max:2048',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'is_active' => 'sometimes|boolean',
        ]);

        $data = $request->only(['title', 'content', 'blog_category_id', 'is_active']);

        if ($request->has('title')) {
            $data['slug'] = Str::slug($request->title) . '-' . $post->id;
        }

        if ($request->hasFile('image')) {
            if ($post->image && Storage::disk('public')->exists($post->image)) {
                Storage::disk('public')->delete($post->image);
            }
            $data['image'] = $request->file('image')->store('posts', 'public');
        }

        $post->update($data);

        if ($request->has('tags')) {
            $post->tags()->sync($request->tags);
        }

        return ApiResponse::success(
            $post->load(['user:id,name', 'category:id,name', 'tags:id,name']),
            'Post updated successfully.'
        );
    }

    public function toggleStatus($id)
    {
        $post = Post::findOrFail($id);
        $post->update(['is_active' => !$post->is_active]);

        $message = $post->is_active 
            ? 'Post published successfully.' 
            : 'Post hidden as draft.';

        return ApiResponse::success($post->load(['user:id,name', 'category:id,name', 'tags:id,name']), $message);
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);

        if ($post->image && Storage::disk('public')->exists($post->image)) {
            Storage::disk('public')->delete($post->image);
        }

        $post->delete();

        return ApiResponse::success(null, 'Post deleted successfully.');
    }
}