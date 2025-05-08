<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\RecoverPasswordController;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\StaticPagesController;

use App\Http\Controllers\SearchController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\UserController;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdvancedFilterController;

use App\Http\Controllers\MessageController;
use App\Http\Controllers\PusherController;


use App\Http\Controllers\NotificationController;

use App\Models\Post;

use App\Http\Controllers\ErrorController;
use App\Http\Controllers\ChatMenuController;
use App\Http\Controllers\GroupController;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Welcome
Route::get('/', function () {
    return view('pages/welcome');
})->name('welcome');

// Home
Route::get('/home/{type}', [HomeController::class, 'index'])->name('home');
Route::get('/notifications', [NotificationController::class, 'showNotifications'])->name('notifications');
Route::middleware('auth')->get('/notifications/unread', [NotificationController::class, 'getUnreadNotifications']);
// Authentication
Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'authenticate');
    Route::get('/logout', 'logout')->name('logout');
});

Route::controller(RegisterController::class)->group(function () {
    Route::get('/register', 'showRegistrationForm')->name('register');
    Route::post('/register', 'register');
});

// SEARCH

Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/search-live', [SearchController::class, 'liveSearch'])->name('search.live'); 
Route::get('/posts/{post}', [SearchController::class,'showPost'])->name('posts.show');



//Profile route
//route for not logged users
Route::get('/users/{userId}/profile', [ProfileController::class, 'show'])->name('profile');
Route::middleware('auth')->group(function () {
    Route::post('/users/{userId}/profile/sendfriendrequest', [UserController::class, 'sendfriendrequest']);
    Route::post('/users/{userId}/profile/acceptfriendrequest', [UserController::class, 'acceptfriendrequest']);
    Route::post('/users/{userId}/profile/rejectfriendrequest', [UserController::class, 'rejectfriendrequest']);
    Route::post('/users/{userId}/profile/removefriendrequest', [UserController::class, 'removefriendrequest']);
    Route::post('/users/{userId}/profile/removefriend', [UserController::class, 'removefriend']);
    Route::post('/blockUser', [UserController::class, 'blockUser'])->name('blockUser');
    Route::post('/unblockUser', [UserController::class, 'unblockUser'])->name('unblockUser');
});
// Edit Profile Only logged-in user can edit their profile
Route::middleware('auth')->group(function () {
    
    Route::get('/users/{userId}/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit')->middleware('can:edit-profile,userId');

    Route::put('/users/{userId}/profile/edit', [ProfileController::class, 'update'])->name('profile.update')->middleware('can:edit-profile,userId');
});


// FRIENDS
Route::get('/friends/{id}', [UserController::class, 'showfriends'])->name('friends.list');
Route::post('/add-friend/{id}', [UserController::class, 'addFriend'])->name('friend.add');
Route::post('/remove-friend/{id}', [UserController::class, 'removeFriend'])->name('friend.remove');
Route::post('/friend_request/{id}', [UserController::class, 'sendFriendRequest'])->name('friend-request.send');
Route::post('/friend_request_remove/{id}', [UserController::class, 'removeFriendRequest'])->name('friend-request.remove');

//---------------------- POST RELATED -----------------------//
Route::controller(PostController::class)->group(function () {
    Route::get('/add-post', 'showAddPostForm')->name('add-post');  // Show form to add post
    Route::post('/add-post', 'savePost')->name('save-post');
    Route::get('/edit-post/{id}', 'showEditPostForm')->name('edit-post');  // Show form to edit post
    Route::put('/edit-post/{id}', 'updatePost')->name('update-post');
    Route::delete('/delete-post/{id}', 'deletePost')->name('delete-post');
    Route::post('/posts/{postId}/like', 'toggleLike')->name('posts.toggleLike');
});
Route::get('/post/view/{id}', function ($id) {
    $post = Post::with(['author', 'media'])->findOrFail($id);
    return view('partials.postView', compact('post'))->render();
})->name('post.view');
//------------------------------------------------------//

//--------------------------------- Comment RELATED --------------------------------//
Route::controller(CommentController::class)->group(function () {
    Route::get('/comments/{id}', 'show')->name('comments.show');
    Route::post('/comments/{id}/like', 'toggleLike')->name('comments.toggleLike');
    Route::post('/comments', 'add')->name('comments.add');
    Route::delete('/comments/{id}', 'destroy')->name('comments.destroy');
    Route::put('/comments/{id}', 'edit')->name('comments.edit');
    Route::post('/comments/{id}/reply', 'addReply')->name('comments.reply');
    Route::get('/comments/{id}/replies', 'getReplies')->name('comments.show.replies');
});
//---------------------------------------------------------------------------------//


//-----------------------------------------------------------------------//
// Esta é para o js, quando queremos dar redirect para o login com mensagem mas tem
// de ser pelo javascript
Route::post('/update-session', function (Request $request) {
    $request->validate([
        'intendedUrl' => 'required|string',
        'redirectReason' => 'required|string',
    ]);

    Session::put('intendedUrl', $request->input('intendedUrl'));
    Session::put('redirectReason', $request->input('redirectReason'));

    return response()->json(['message' => 'Session updated successfully.']);
});
//----------------------------------------------------------------------//

//-------------------RECOVER PASSWORD---------------------------------------------------//

Route::controller(RecoverPasswordController::class)->group(function () {
    Route::get('/recoverPassword', 'showRecoverPasswordForm')->name('recoverPassword');
    Route::post('/recoverPassword', 'recoverPass');
    Route::get('/verifyCode', 'showVerifyCodeForm')->name('verifyCode');
    Route::post('/verifyCode', 'verifyCode');
});
//--------------------------------------------------------------------------------------//


//------------------Message---------------------------------------------------//

Route::middleware(['auth'])->group(function () {
    Route::get('/chatMenu', [ChatMenuController::class, 'index'])->name('chatMenu.index');
    Route::get('/messages/{recipient}', [MessageController::class, 'index'])->name('messages.index');
    Route::post('/broadcast/{recipient}', [MessageController::class, 'broadcast'])->name('broadcast');
    Route::post('/receive', [MessageController::class, 'receive'])->name('receive');
    Route::post('/pusher/auth', [PusherController::class, 'pusherAuth']);
    Route::post('/sharePost', [MessageController::class, 'sharePost']);



});

//------------------Groups---------------------------------------------------//

Route::get('/groupsCreate', [GroupController::class, 'create'])->name('groups.create');
Route::post('/groups', [GroupController::class, 'store'])->name('groups.store');
Route::get('/groupAddUsers/{groupName}', [GroupController::class, 'index_add_users'])->name('groups.add');
Route::get('/groupRemoveUsers/{groupName}', [GroupController::class, 'index_remove_users'])->name('groups.remove');
Route::get('/groupEditUsers/{groupName}', [GroupController::class, 'index_edit_group'])->name('groups.edit');

Route::get('/search-liveG', [GroupController::class, 'liveSearchAddUsers']);
Route::post('/send-invites', [GroupController::class, 'sendInvites']);
Route::post('/delete-users', [GroupController::class, 'removeMembers']);
Route::put('/groups/{group}', [GroupController::class, 'updateGroup'])->name('groups.update');
Route::delete('/groups/{group}', [GroupController::class, 'destroyGroup'])->name('groups.destroy');
Route::post('/groups/leave/{group}', [GroupController::class, 'leaveGroup'])->name('groups.leave');



//--------------------------------------------------------------------------------------//
//--------------------------------------Static Pages---------------------------------------------------//
Route::get('/about', function () {
    return view('pages/about');
})->name('about');

Route::get('/contact', function () {
    return view('pages/contact');
})->name('contact');

Route::get('/terms', function () {
    return view('pages/terms');
})->name('terms');

Route::get('/privacy', function () {
    return view('pages/privacy');
})->name('privacy');

//----------------------------------------------------------------------------------------------------//  


Route::controller(UserController::class)->group(function () {
    Route::delete('/deleteUser/{userId}', 'deleteUser')->name('deleteUser');
    Route::delete('/removeAdmin/{userId}', 'removeAdmin')->name('removeAdmin');
    Route::post('/AdminBlockUser/{userId}',  'AdminBlockUser')->name('AdminBlockUser');
    Route::delete('/AdminUnblockUser/{userId}',  'AdminUnblockUser')->name('AdminUnblockUser');
});

Route::get('/404', [ErrorController::class, 'notFound']);
