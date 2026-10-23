<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use App\Traits\PaginationTrait;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    use PaginationTrait;

    public function index(Request $request)
    {
        $query = Comment::with(['post:id,title', 'user:id,name', 'parent:id,name']);

        if ($request->has('post_id') && $request->post_id != '') {
            $query->where('post_id', $request->post_id);
        }

        $comments = $query->latest()->paginate($this->getPerPage($request, 20));

        return ApiResponse::paginated($comments, 'Comments retrieved successfully.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'post_id' => 'required|exists:posts,id',
            'parent_id' => 'nullable|exists:comments,id',
            'comment' => 'required|string',
        ]);

        $user = $request->user();

        $comment = Comment::create([
            'post_id' => $request->post_id,
            'parent_id' => $request->parent_id,
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'website' => null,
            'comment' => $request->comment,
        ]);

        return ApiResponse::success(
            $comment->load(['user:id,name', 'parent:id,name']),
            'Reply added successfully.',
            201
        );
    }

    public function show($id)
    {
        $comment = Comment::with(['post:id,title', 'user:id,name', 'parent:id,name', 'replies'])
            ->findOrFail($id);

        return ApiResponse::success($comment, 'Comment retrieved successfully.');
    }

    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();

        return ApiResponse::success(null, 'Comment deleted successfully.');
    }
}