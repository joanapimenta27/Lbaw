<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;
use App\Models\PostLike;
use App\Events\PostLikes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{

    public function showAddPostForm()
    {
        if (!Auth::check()) {
            session([
                'intendedUrl' => url()->full(),
                'redirectReason' => 'Log in to have a profile :)',
            ]);
            return redirect()->route('login');
        }
        return view('posts/add');
    }

    //Save post into the database.
    public function savePost(Request $request)
    {

        $fileOrder = json_decode($request->input('file_order'), true);
        $request->merge(['file_order' => $fileOrder]);

        DB::beginTransaction();
        //Input validation
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'nullable|array|max:7',
            'content.*' => 'nullable|file|mimes:webp,jpg,jpeg,png,mp4,mov,avi,mkv|max:20480',
            'file_order' => 'nullable|array',
            'file_order.*' => 'string',
        ]);

        $post = Post::create([
            'author_id' => auth()->user()->id,  
            'title' => $request->title,        
            'description' => $request->description, 
            'date' => now(),                 
            'is_public' => $request->has('is_public') ? true : false,
        ]);

        $fileOrder = $request->input('file_order', []);
        $currentOrder = 0;

        //File path handling >_<
        if ($request->hasFile('content')) {
            $newFiles = $request->file('content');
    
            foreach ($newFiles as $file) {
                // Determine file type (image or video)
                $mimeType = $file->getMimeType();
                $fileType = str_starts_with($mimeType, 'image') ? 'image' : 'video';
    
                // Store file
                $path = $file->store("uploads/user_{$post->author_id}/post_{$post->id}", 'public');
    
                // Check file's position in the `file_order` array
                $fileIndex = array_search("new_{$file->getClientOriginalName()}", $fileOrder);
                $order = $fileIndex !== false ? $fileIndex : $currentOrder++;
    
                // Save to database
                $post->media()->create([
                    'file_path' => $path,
                    'file_type' => $fileType,
                    'order' => $order,
                ]);
            }
        }  
        DB::commit();

        //Redirect to profile page.
        return redirect()->route('profile', ['userId' => auth()->user()->id])->with('success', 'Post created successfully!');    
    }

    public function deletePost($id)
    {
        $post = Post::findOrFail($id);

        // Optional: Authorization logic to check if the user can delete this post
        if (auth()->user()->cannot('delete', $post)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $post->delete();

        return response()->json(['success' => 'Post deleted successfully'], 200);
    }

    public function showEditPostForm($id)
    {
        $post = Post::with('media')->findOrFail($id);

        // Authorization to ensure only the author can edit the post
        if(Auth::check()){
            if (auth()->user()->cannot('update', $post)) {
                return redirect()->route('home', ['type' => 'public'])->with('error', 'Unauthorized to edit this post.');
            }
        }
        else{
            return redirect()->route('home', ['type' => 'public'])->with('error', 'not authenticated');
        }

        return view('posts/edit', compact('post'));
    }
    

    //------------------------------------------------------------------------------------------------//
    public function updatePost(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        // Authorization check
        if (auth()->user()->cannot('update', $post)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $fileOrder = json_decode($request->input('file_order'), true);
        $request->merge(['file_order' => $fileOrder]);

        // Validate the input
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'nullable|array|max:7',  // Up to 7 media files
            'content.*' => 'file|mimes:webp,jpg,jpeg,png,mp4,mov,avi|max:10240',  // Validation for each file
            'existing_files' => 'nullable|array',  // IDs of existing files to keep
            'existing_files.*' => 'integer|exists:post_media,id', // Ensure these IDs exist in post_media
            'file_order' => 'sometimes|array',
            'file_order.*' => 'nullable|string',
        ]);

        // Update post data
        $post->title = $request->title;
        $post->description = $request->description;
        $post->is_public = $request->has('is_public') ? true : false;
        $post->save();

        // Handle existing media files: Remove files that are not in `existing_files`
        $existingFilesIds = $request->input('existing_files', []); // Get the IDs of existing files to keep
        $mediaToDelete = $post->media()->whereNotIn('id', $existingFilesIds)->get();

        foreach ($mediaToDelete as $media) {
            // Delete the file from storage
            Storage::disk('public')->delete($media->file_path);

            // Delete the database entry for the old media
            $media->delete();
        }

        // Process existing files and their new order
        if ($request->filled('file_order')) {
            foreach ($request->input('file_order') as $index => $fileId) {
                // Check if the file is new or existing
                if (str_starts_with($fileId, 'existing_')) {
                    // Process existing files
                    $existingId = str_replace('existing_', '', $fileId);
                    $media = $post->media()->find($existingId);
        
                    if ($media) {
                        $media->update(['order' => $index]);
                    }
                } elseif (str_starts_with($fileId, 'new_')) {
                    // Skip new files here as they are processed separately in the "Handle new files" section
                    continue;
                }
            }
        }

        // Handle new files
        if ($request->hasFile('content')) {
            $newFiles = $request->file('content');
            $currentOrder = $post->media()->count(); // Start order after existing media
        
            foreach ($newFiles as $file) {
                $filename = 'new_' . $file->getClientOriginalName();
        
                // Check the file's position in the `file_order` array
                $fileIndex = array_search($filename, $request->input('file_order', []));
                $order = $fileIndex !== false ? $fileIndex : $currentOrder++;
        
                // Determine file type (image or video)
                $mimeType = $file->getMimeType();
                $fileType = str_starts_with($mimeType, 'image') ? 'image' : 'video';
        
                // Store file
                $path = $file->store("uploads/user_{$post->author_id}/post_{$post->id}", 'public');
        
                // Save to database
                $post->media()->create([
                    'file_path' => $path,
                    'file_type' => $fileType,
                    'order' => $order,
                ]);
            }
        }


        return redirect()->route('profile', ['userId' => auth()->user()->id])->with('success', 'Post updated successfully!'); 
    }

    public function toggleLike(Request $request, $postId) {
        // Check if the user is authenticated
        if (!Auth::check()) {
            return response()->json([
                'error' => 'Unauthenticated',
                'message' => 'Log in to like posts.',
                'redirect_url' => route('login'),
            ], 401); // 401 Unauthorized status code
        }

        try {
            $user = Auth::user();
            $post = Post::findOrFail($postId); // Ensure the post exists

            // Check if the user already liked the post
            $like = PostLike::where('user_id', $user->id)
                ->where('post_id', $postId)
                ->first();

            if ($like) {
                // Unlike the post
                $like->delete();
                $post->decrement('like_num'); // Decrement the like count on the post

                return response()->json([
                    'status' => 'unliked',
                    'like_num' => $post->like_num,
                ]);
            } else {
                // Like the post - use save() instead of create()
                $postLike = new PostLike;
                $postLike->user_id = $user->id;
                $postLike->post_id = $postId;
                $postLike->save(); // Save the new like


                event(new PostLike(['author_id' => $post->author_id, 'post_id' => $postId]));


                return response()->json([
                    'status' => 'liked',
                    'like_num' => $post->like_num,
                ]);
            }
        } catch (\Exception $e) {
            // Handle unexpected errors
            return response()->json([
                'error' => 'Something went wrong.',
                'message' => $e->getMessage(),
            ], 500); // 500 Internal Server Error status code
        }
    }
}

