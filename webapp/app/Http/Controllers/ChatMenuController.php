<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class ChatMenuController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $groups =  $this->getgroups($userId);
        $chatsWithRoutes = $this->getChats($userId);

        return view('pages.ChatMenu', ['chats' => $chatsWithRoutes, 'groups' => $groups]);
    }

    public function getChats($userId)
    {
        $chats = User::friendsAndChat($userId);

        if ($chats->isEmpty()) {
            return collect();
        }

        $chatsWithRoutes = $chats->map(function ($chat) use ($userId) {
            $recipientString = min($userId, $chat->id) . '.' . max($userId, $chat->id);
            
            $chat->route = route('messages.index', ['recipient' => $recipientString]);
            return $chat;
        });

        return $chatsWithRoutes;
    }
    public function getgroups($userId)
    {
        $groups = User::getAllGroups($userId);
    
        if ($groups->isEmpty()) {
            return collect();
        }
    
        $groupsWithRoutes = $groups->map(function ($group) use ($userId) {
            $groupString = "group:{$group->name}";
            $group->route = route('messages.index', ['recipient' => $groupString]);
            return $group;
        });
    
        return $groupsWithRoutes;
    }
    
    
}
