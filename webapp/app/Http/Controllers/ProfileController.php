<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Post;
use App\Models\User;
use App\Models\PostMedia; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Admin;



class ProfileController extends Controller
{
    
  public function show($userID)
  {
    if (!Auth::check()) {
      session([
          'intendedUrl' => route('home', 'foryou'),
          'redirectReason' => 'Log in to have a profile!',
      ]);
      return redirect()->route('login');
    }
    
    $user = User::with('posts')->findOrFail($userID);  // load posts relationship
    $currentUser = Auth::user();
    error_log("user name:".$user);

    if($user->hasBlocked($currentUser)){
      return redirect()->route('search')->with('error', 'You cannot view this profile because you have been blocked by this user.');
    }
    if ($currentUser) {
      if ($currentUser->id === $user->id || $currentUser->isFriendWith($user) || $currentUser->isAdmin()) {
          // Load all posts of the user, including their media
          $posts = $user->posts()->with('media')->orderBy('date', 'desc')->get();
      } else {
          // Load only public posts, including their media
          $posts = $user->posts()->where('is_public', true)->with('media')->orderBy('date', 'desc')->get();
      }
    } else {
        // Load only public posts for unauthenticated users, including their media
        $posts = $user->posts()->where('is_public', true)->with('media')->orderBy('date', 'desc')->get();
    }
  
      return view('profile.profilePage', ['user' => $user, 'posts' => $posts]);
  }

  public function edit($userId)
  {
    \Log::info("Editing profile for User ID: {$userId}");
      $user =  User::findOrFail($userId)->load('posts'); //post for the feature edit or delete post
      return view('profile.profileEdit', compact('user'));

  }
  public function update(Request $request,$userId)  {
    if(Auth::user()->isAdmin()){
      $validationRules = [
        'name' => 'required|string|max:250',
        'profile_picture' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
      ];

    }
    else{
      $validationRules = [
        'name' => 'required|string|max:250',
        'profile_picture' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        'current_password' => 'required|string',
        'password' => 'nullable|string|min:6',
       ];
    }
   

    $user=User::findOrFail($userId);


    if($request->username!=$user->username){
      $validationRules['username'] = 'required|string|max:250|unique:users';
      
    }
    if($request->email!=$user->email){
      $validationRules['email'] = 'required|email|max:250|unique:users';
    }

    $request->validate($validationRules);
    
    if(!Auth::user()->isAdmin() ){
      if(!Hash::check($request->current_password,$user->password)){
        return back()->withErrors(['current_password' => 'The current password is incorrect.']);
      }
    }
    

    $user->name=$request->name;
    $user->username=$request->username;
    $user->email=$request->email;
    if ($request->hasFile('profile_picture')) {
      if ($user->profile_picture) {
          Storage::delete('public/' . $user->profile_picture);
      }
  
      $file = $request->file('profile_picture');
      $path = $file->store("uploads/{$user->id}/profile_picture", 'public');
  
      
      $user->profile_picture = $path;
      $user->save();
  }

    // TODO: Update file
    //Save the user
    if($request->password){
      $user->password=$request->password;
    }

    $user->save();

    if($request->has('admin_checkbox') && !$user->isAdmin()){
      Admin::create([
          'user_id' => $user->id ,  
          'is_super' => false,
    
      ]);
    }
    else if ($user->isAdmin() && !$request->has('admin_checkbox')){
      $admin = Admin::where('user_id', $user->id)->first();
      $admin->delete();
    }

    return redirect()->route('profile', $user->id)->with('success', 'Profile updated successfully!');


    
  }



}
