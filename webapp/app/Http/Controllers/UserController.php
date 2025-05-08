<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\DeletedUser;
use App\Models\User;
use App\Models\Friendrequest;
use App\Models\Friend;
use App\Models\Notification;
use App\Models\Blockfriend;
use App\Models\BlockedUser;

class UserController extends Controller
{
    public function deleteUser($userId)
    {
        Log::info('Delete user function called');
        try {

            // Call the SQL function to anonymize the user
            DB::select('SELECT delete_user(?)', [$userId]);

            // Get the admin status and user ID of the authenticated user
            $authUser = Auth::user();
            $isAdmin = $authUser->isAdmin();
            $authUserId = $authUser->id;
            $isSuper = $authUser->isSuper();

            return response()->json(['message' => 'User anonymized successfully', 'isAdmin' => $isAdmin, 'authUserId' => $authUserId, 'isSuper' => $isSuper], 200);
        } catch (\Exception $e) {
            Log::error('Failed to delete user: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to delete user', 'error' => $e->getMessage()], 500);
        }
    }

    public function removeAdmin($userId)
    {
        Log::info('Remove admin function called');
        try {
            // Call the SQL function to remove the user from the admin table
            DB::select('SELECT remove_user_from_admin(?)', [$userId]);

            return response()->json(['message' => 'User removed from admin successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Failed to remove user from admin: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to remove user from admin', 'error' => $e->getMessage()], 500);
        }
    }




    public function sendfriendrequest(Request $request, $id)
    {
        $receiver = User::findorFail($id);  
        $sender = Auth::user();

         if ($receiver->isBlocked($sender)) {
            return response()->json(['error' => 'You are blocked by this user.'], 403);
        }
        $this->authorize('sendFriendRequest', $receiver);

        
       
        DB::beginTransaction();

        Friendrequest::insert([
            'req_id' => $sender->id,
            'rcv_id' => $receiver->id,
        ]);
        

       
        DB::commit();

        return response()->json(['success' => true, 'message' => 'Friend request sent.']);
       
    }

    public function acceptfriendrequest(Request $request, $id)
    {
        $receiver = User::findorFail($id);

        $sender = Auth::user();
       
        $this->authorize('acceptFriendRequest', $sender);

        DB::beginTransaction();

        Friendrequest::where('req_id', $receiver->id)->where('rcv_id', $sender->id)->delete();

        Friend::insert([
            'user_id' => $receiver->id,
            'friend_id' => $sender->id,
        ]);
        Friend::insert([
            'user_id' => $sender->id,
            'friend_id' => $receiver->id,
        ]);

       

        DB::commit();
    }

    public function rejectfriendrequest(Request $request, $id)
    {
        $receiver = User::findorFail($id);

        $sender = Auth::user();
        $this->authorize('rejectFriendRequest', $sender);

        DB::beginTransaction();

        Friendrequest::where('req_id', $sender->id)->where('rcv_id', $receiver->id)->delete();

        
        DB::commit();
    }

    public function removefriendrequest(Request $request, $id)
{
    try {
        $receiver = User::findorFail($id);

        $sender = Auth::user();
       
        $this->authorize('removeFriendRequest', $receiver);

        DB::beginTransaction();

        // Remover a solicitação de amizade
        Friendrequest::where('req_id', $sender->id)->where('rcv_id',$receiver->id,)->delete();
    

        // Criação de notificação
      

        DB::commit();

        return response()->json(['success' => true, 'message' => 'Friend request removed.']);
    } catch (\Exception $e) {
        DB::rollBack();
        // Log de erro para rastrear o problema
        \Log::error('Error in removefriendrequest: ' . $e->getMessage());
        return response()->json(['error' => 'Internal Server Error'], 500);
    }
}

public function removefriend(Request $request, $id)
{
    try {
        $receiver = User::findorFail($id);

        $sender = Auth::user();
        Log::info('sender: ' . $sender->id);
        Log::info('receiver: ' . $receiver->id);
       
        $this->authorize('removeFriend', $receiver);

        DB::beginTransaction();

        // Remover a amizade
        Friend::where('user_id', $receiver->id)->where('friend_id', $sender->id)->delete();
        Friend::where('user_id', $sender->id)->where('friend_id', $receiver->id)->delete();
    
        DB::commit();

        return response()->json(['success' => true, 'message' => 'Friend removed.']);
    } catch (\Exception $e) {
        DB::rollBack();
        // Log de erro para rastrear o problema
        \Log::error('Error in removefriend: ' . $e->getMessage());
        return response()->json(['error' => 'Internal Server Error'], 500);
    }
   
}

    public function showFriends($id)
    {
        $user = User::findOrFail($id);
        $friends = $user->friends; 
        return view('pages.friendspage', compact('user', 'friends'));
    }

    public function blockUser(Request $request)
    {
        $userId = $request->input('userId');
        $authId = Auth::id();

        Log::info('Blocking user function called');
        try {
            // Insert the user ID into the block_friend table
            DB::table('block_friend')->insert([
                'blocker_id' => $authId,
                'blocked_id' => $userId
            ]);

            return response()->json(['success' => true, 'message' => 'User blocked successfully']);
        } catch (\Exception $e) {
            Log::error('Failed to block user: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to block user', 'error' => $e->getMessage()]);
        }
    }

    public function unblockUser(Request $request)
    {
        $userId = $request->input('userId');
        $authId = Auth::id();

        Log::info('Unblocking user function called');
        try {
            // Delete the user ID from the block_friend table
            DB::table('block_friend')->where([
                ['blocker_id', '=', $authId],
                ['blocked_id', '=', $userId]
            ])->delete();

            return response()->json(['success' => true, 'message' => 'User unblocked successfully']);
        } catch (\Exception $e) {
            Log::error('Failed to unblock user: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to unblock user', 'error' => $e->getMessage()]);
        }
    }
    
    public function AdminBlockUser($userId)
    {
        Log::info('Admin_blocking user function called');
        try {
            // Insert the user ID into the blocked_users table
            BlockedUser::create(['user_id' => $userId]);

            return response()->json(['message' => 'User blocked by admin successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Failed to block user: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to block user', 'error' => $e->getMessage()], 500);
        }
    }

    public function AdminUnblockUser($userId)
    {
        Log::info('Admin_unblocking user function called');
        try {
            // Delete the user ID from the blocked_users table
            BlockedUser::where('user_id', $userId)->delete();

            return response()->json(['message' => 'User unblocked by admin successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Failed to unblock user: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to unblock user', 'error' => $e->getMessage()], 500);
        }
    }

}