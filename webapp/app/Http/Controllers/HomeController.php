<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\PostMedia; 

class HomeController extends Controller
{
    public function index($feed_type)
    {
        // Check if user is authenticated
        $user = auth()->user();

        // Handle public feed
        if ($feed_type === 'public') {
            // Retrieve only public posts
            $posts = Post::getPublicPosts()->paginate(10);
            if (request()->ajax()) {
                return view('partials.post-list', ['posts' => $posts])->render();
            }
    
            return view('pages/home', [
                'posts' => $posts,
                'isAdmin' => $user ? $user->isAdmin() : false,
            ]);
        }

        // If user is not authenticated and tries to access a feed type that requires login
        if (!$user) {
            return redirect()->route('home', ['type' => 'public']);
        }

        // Handle private feed (TODO: implement logic to show posts from friends)
        if ($feed_type === 'foryou') {
            $posts = Post::getPrivateFeed($user->id)->paginate(10); // Paginate private feed

            // Check if the request is an AJAX request for lazy loading
            if (request()->ajax()) {
                return view('partials.post-list', ['posts' => $posts])->render();
            }

            return view('pages/home', [
                'posts' => $posts,
                'isAdmin' => $user->isAdmin(),
            ]);
        }

        // Default to public if feed type is unknown
        return redirect()->route('home', ['type' => 'public']);
    }
}