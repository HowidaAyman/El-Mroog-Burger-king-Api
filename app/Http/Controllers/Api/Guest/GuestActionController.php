<?php

namespace App\Http\Controllers\Api\Guest;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Contact;
use App\Models\Post;
use App\Models\Reservation;
use Illuminate\Http\Request;

class GuestActionController extends Controller
{
    public function storeReservation(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => ['required', 'string', 'regex:/^\+?[0-9\s\-\(\)]{7,20}$/'],
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i',
            'guests' => 'required|integer|min:1|max:50',
            'notes' => 'nullable|string|max:1000',
        ]);

        $reservation = Reservation::create($request->all());

        return ApiResponse::success($reservation, 'Your reservation has been submitted successfully. We will contact you soon.', 201);
    }

    public function storeContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        $contact = Contact::create($request->all());

        return ApiResponse::success($contact, 'Your message has been sent successfully.', 201);
    }

    public function storeComment(Request $request, $postId)
    {
        $post = Post::findOrFail($postId);

        $request->validate([
            'parent_id' => 'nullable|exists:comments,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'website' => 'nullable|url|max:255',
            'comment' => 'required|string|max:2000',
        ]);

        if ($request->parent_id) {
            $parent = Comment::findOrFail($request->parent_id);
            if ($parent->post_id !== $post->id) {
                return ApiResponse::error('Invalid parent comment for this post.', 400);
            }
        }

        $comment = Comment::create([
            'post_id' => $post->id,
            'parent_id' => $request->parent_id,
            'user_id' => null,
            'name' => $request->name,
            'email' => $request->email,
            'website' => $request->website,
            'comment' => $request->comment,
        ]);

        return ApiResponse::success($comment, 'Your comment has been submitted successfully.', 201);
    }
}