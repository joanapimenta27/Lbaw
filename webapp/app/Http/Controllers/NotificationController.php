<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\policies\NotificationPolicy;



class NotificationController extends Controller
{
    protected function usernotifications(Request $request)
    {
        return Notification::join('users', 'users.id', '=', 'notifications.receiver_id')
            ->where('receiver_id', Auth::user()->id)
            ->select('notifications.*', 'users.name')
            ->get();
    }
    protected function postnotifications(Request $request)
    {
        return Notification::join('posts', 'posts.id', '=', 'notifications.post_id')
            ->where('receiver_id', Auth::user()->id)
            ->select('notifications.*', 'posts.title')
            ->get();
    }
    protected function adminnotifications(Request $request)
    {
        return Notification::join('admins', 'admins.id', '=', 'notifications.receiver_id')
            ->where('receiver_id', Auth::user()->id)
            ->select('notifications.*', 'admins.name')
            ->get();
    }
    protected function commentnotifications(Request $request)
    {
        return Notification::join('comments', 'comments.id', '=', 'notifications.comment_id')
            ->where('receiver_id', Auth::user()->id)
            ->select('notifications.*', 'comments.content')
            ->get();
    }
    protected function messagenotifications(Request $request)
    {
        return Notification::join('messages', 'messages.id', '=', 'notifications.message_id')
            ->where('receiver_id', Auth::user()->id)
            ->select('notifications.*', 'messages.content')
            ->get();
    }
    protected function friendnotifications(Request $request)
    {
        return Notification::join('users', 'users.id', '=', 'notifications.sender_id')
            ->where('receiver_id', Auth::user()->id)
            ->select('notifications.*', 'users.name')
            ->get();
    }
    public function delete(Request $request)
    {
        $this->authorize('delete', Notification::class);

        DB::beginTransaction();
        commentnotifications::where('id', $request->id)->delete();
        friendnotifications::where('id', $request->id)->delete();
        usernotifications::where('id', $request->id)->delete();
        postnotifications::where('id', $request->id)->delete();
        adminnotifications::where('id', $request->id)->delete();
        messagenotifications::where('id', $request->id)->delete();
        DB::commit();
    }
    protected function notification(Request $request)
    {
        return count(Notification::select('id')->where('receiver_id', Auth::user()->id)->where('seen', false)->get());
    }
    protected function seen(Request $request)
    {
        foreach ($request->all() as $notification) {
            Notification::where('id', $notification['id'])->update(['seen' => true]);
        }
    }
    public function get_notifications(Request $request)
    {
        $this->authorize('get_notifications', Notification::class);

        switch ($request->input('type')) {
            case 'user':
                return $this->usernotifications($request);
            case 'post':
                return $this->postnotifications($request);
            case 'admin':
                return $this->adminnotifications($request);
            case 'comment':
                return $this->commentnotifications($request);
            case 'message':
                return $this->messagenotifications($request);
            case 'friend':
                return $this->friendnotifications($request);
            default:
                return response()->json(['error' => 'Invalid notification type'], 400);
        }
    }
public function showNotifications()
{
    $notifications = Notification::where('receiver_id', Auth::user()->id)
                                 ->orderBy('date', 'desc')
                                 ->get();

    // Format the date correctly
    foreach ($notifications as $notification) {
        $notification->date = \Carbon\Carbon::parse($notification->date)->toIso8601String();
    }

    return view('pages.notificationpage', ['notifications' => $notifications]);
}

   public function countUnreadNotifications()
{
    $unreadCount = Notification::where('receiver_id', Auth::user()->id)
                               ->where('seen', false)
                               ->count();
    return response()->json(['unread_count' => $unreadCount]);
}
public function getUnreadNotifications()
{
    try {
        $user = auth()->user();
        if (!$user) {
            throw new \Exception('User not authenticated');
        }

        // Carregar notificações com o relacionamento user
        $notifications = Notification::with('user') // Aqui estamos carregando o relacionamento 'user'
                                     ->where('receiver_id', $user->id)
                                     ->where('seen', false)
                                     ->orderBy('date', 'desc')
                                     ->get();

        return response()->json($notifications);
    } catch (\Exception $e) {
        \Log::error('Failed to fetch notifications', ['error' => $e->getMessage()]);
        return response()->json(['error' => 'Failed to fetch notifications', 'message' => $e->getMessage()], 500);
    }
}

}