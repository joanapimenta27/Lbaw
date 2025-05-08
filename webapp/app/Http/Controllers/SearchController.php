<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;



class SearchController extends Controller
{
  
    public function index(Request $request)
    {
        $tag = $request->get('tag');
        $attributes = $this->getAdvanceSettingBytag($request);
        return view('pages.search', compact('attributes', 'tag'));
    }

   
   

    public function liveSearch(Request $request)
    {
        $query = $request->get('search'); 
        $tag = $request->get('tag');
        $filter = $request->query('filter');
        $values=[];

        switch ($tag) {
            case 'accounts':
            case null:
               
                /* $values = User::where('username', 'LIKE', '%' . $query . '%')
                ->whereDoesntHave('deletedUser')
                ->get();
 */
                if($filter=="name"){
                    $values = User::getUserByName($query);
                }
                else if($filter=="email"){
                    $values = User::getUserByEmailQuery($query);
                }
                else{
                    $values = User::getUserByUserName($query);
                }

                 break;

            case 'titles':
                $values = $this->getpostFulltextSearch($query,$filter);
                error_log('Posts values  : ' . $values->count());
                break;
                
            case 'tags':
                $values =Post::searchPostsByTags($query);
                break;
            
            default:
            error_log("default" );
                
                break;
        }
        return response()->json($values);
    }

        public function getpostFulltextSearch($query,$filter) {
            $posts=[];
            if($query==NULL || $query==''){
                $posts = Post::getFeedPosts($filter);
            }
            else{
                $posts = $this->handleFulltextResult($query,$filter);  
            }
            
            return  $posts; 
        }


        public function handleFulltextResult($query, $filter) {
            $posts = collect(); 
        
            if ($filter === null || ($filter != 'description' && $filter != 'title')) {
                $posts = collect(Post::getPostByFullTextSearch($query));
        
                if ($posts->count() > 0) {
                    return $posts;
                }
            }
        
            $posts = collect(Post::searchPostsByQuery($query, $filter));
            error_log('Here brroooooo' . $filter);
            return $posts;
        }
        

        public function getAdvanceSettingBytag(Request $request)
        {
            $tag = $request->get('tag');
            $attributes = [];
        
            switch ($tag) {
                case 'accounts':
                case null:
                    $attributes = ['name', 'email'];
                    break;
                case 'titles':
                    $attributes = ['older', 'title', 'description'];
                    break;
                default:
                    $attributes = [];
                    break;
            }
        
            return $attributes;
        }


        public function showPost($post)
    {
        $postValue=Post::find($post);
        return view('partials.post',  ['post' => $postValue]);
    }
        
    
    }