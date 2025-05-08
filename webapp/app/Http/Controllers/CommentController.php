<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Comment;
use App\Models\Post;

class CommentController extends Controller
{
    public function show($id, Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'error' => 'Unauthenticated',
                'message' => 'Log in to comment on post.',
                'redirect_url' => route('login'),
            ], 401); // 401 Unauthorized status code
        }

        $limit = $request->query('limit', 10);
        $offset = $request->query('offset', 0);

        $post = Post::findOrFail($id);

        $comments = $post->comments()
            ->whereNull('parent_id')
            ->with('user')
            ->orderBy('date', 'desc')
            ->skip($offset)
            ->take($limit)
            ->get();

        $html = $comments->map(function ($comment) {
            return view('partials.comment', ['comment' => $comment, 'isReply' => false])->render();
        })->implode('');

        $totalComments = $post->comments()->whereNull('parent_id')->count();
        $hasMore = $totalComments > ($offset + $limit);

        return response()->json([
            'html' => $html,
            'hasMore' => $hasMore
        ]);
    }


    public function add(Request $request)
    {
        $request->validate([
            'post_id' => 'required|exists:post,id',
            'content' => 'required|string|max:1500',
            'parent_id' => 'nullable|exists:comment,id',
        ]);

        $comment = Comment::create([
            'user_id' => Auth::id(),
            'post_id' => $request->post_id,
            'parent_id' => $request->parent_id,
            'content' => $request->content,
            'date' => now(),
            'like_num' => 0,
            'reply_num' => 0,
        ]);

        // Update reply count for parent comment, if applicable
        if ($comment->parent_id) {
            Comment::where('id', $comment->parent_id)->increment('reply_num');
        }

        $isReply = $comment->parent_id !== null;
        
        $html = view('partials.comment', ['comment' => $comment, 'isReply' => $isReply])->render();

        return response()->json(['html' => $html]);
    }


    public function toggleLike($id)
    {
        $userId = Auth::id();

        if (!$userId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $comment = Comment::findOrFail($id);

        // Check if already liked
        $alreadyLiked = $comment->likes()->where('user_id', $userId)->exists();

        if ($alreadyLiked) {
            // Unlike the comment
            $comment->likes()->detach($userId);
            return response()->json(['status' => 'unliked', 'like_num' => $comment->like_num]);
        } else {
            // Like the comment
            $comment->likes()->attach($userId);
            return response()->json(['status' => 'liked', 'like_num' => $comment->like_num]);
        }
    }


    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);
    
        if (Auth::id() !== $comment->user_id && !Auth::user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
    
        if ($comment->parent_id) {
            Comment::where('id', $comment->parent_id)->decrement('reply_num');
        }

        Post::where('id', $comment->post_id)->decrement('comment_num');
    
        $comment->delete();
    
        return response()->json(['message' => 'Comment deleted successfully!']);
    }

    public function edit(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string|max:1500',
        ]);

        $comment = Comment::findOrFail($id);

        // Ensure the user owns the comment or is an admin
        if (Auth::id() !== $comment->user_id && !Auth::user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comment->update([
            'content' => $request->content,
            'edited' => true,
        ]);

        return response()->json(['message' => 'Comment updated successfully!']);
    }

    public function addReply(Request $request)
    {   
        $request->validate([
            'content' => 'required|string|max:1500',
            'parent_id' => 'required|exists:comment,id',
        ]);

        $parentComment = Comment::findOrFail($request->parent_id);
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $finalParentId = $parentComment->parent_id ?? $parentComment->id;
        $reply = Comment::create([
            'user_id' => $user->id,
            'post_id' => $request->post_id,
            'parent_id' => $finalParentId,
            'content' => $request->content,
            'date' => now(),
        ]);

        $reply->load('user');
        $comment = $reply;
        $html = view('partials.comment', ['comment' => $comment, 'isReply' => true])->render();

        return response()->json(['html' => $html]);
    }

    public function getReplies($id, Request $request)
    {
        $limit = $request->query('limit', 5);
        $offset = $request->query('offset', 0);

        $comment = Comment::findOrFail($id);

        $replies = $comment->replies()
            ->with('user')
            ->skip($offset)
            ->take($limit + 1)
            ->get();

        $hasMoreReplies = $replies->count() > $limit;

        if ($hasMoreReplies) {
            $replies = $replies->slice(0, $limit);
        }

        $html = $replies->map(function ($reply) {
            return view('partials.comment', ['comment' => $reply, 'isReply' => true])->render();
        })->implode('');

        return response()->json(['html' => $html, 'hasMoreReplies' => $hasMoreReplies]);
    }
}