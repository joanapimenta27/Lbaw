<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Pusher\Pusher;
use App\Models\Group;


class PusherController extends Controller
{
    public function pusherAuth(Request $request)
    {
        error_log('Incoming Request Data: ' . json_encode($request->all()));

        if (!$request->has(['channel_name', 'socket_id'])) {
            error_log('Error: Missing channel_name or socket_id in request.');
            return response()->json(['error' => 'Missing parameters.'], 400);
        }

        $user = auth()->user();

        if (!$user) {
            error_log('Error: No authenticated user.');
            return response()->json(['error' => 'User not authenticated.'], 403);
        }

        $channelName = $request->input('channel_name');
        $socketId = $request->input('socket_id');
        $group=$request->input('group');
        if($group==null){
            if (!$this->userCanAccessChannel($user, $channelName)) {
                error_log("Error: User {$user->id} is not authorized to access {$channelName}");
                return response()->json(['error' => 'Unauthorized access.'], 403);
            }

        }else{
            if (!$this->userCanAccessChannelGroup($user, $channelName,$group)) {
                error_log("Error: User {$user->id} is not authorized to access {$channelName}");
                return response()->json(['error' => 'Unauthorized access.'], 403);
            }

        }
        try {
            $pusher = new Pusher(
                env('PUSHER_APP_KEY'),
                env('PUSHER_APP_SECRET'),
                env('PUSHER_APP_ID'),
                ['cluster' => env('PUSHER_APP_CLUSTER')]
            );

            return response($pusher->authorizeChannel($channelName, $socketId));
        } catch (\Exception $e) {
            error_log('Error authorizing channel: ' . $e->getMessage());
            return response()->json(['error' => 'Server error during authorization.'], 500);
        }
    }


    private function userCanAccessChannelGroup($user, $channelName,$group){
        $auth=false;
        $allowUsers= $group->members;
        if($allowUsers->contains($user)){
            $auth=true;
        }

        error_log("Authorization check for user {$user->id}: " . ($isAuthorized ? 'Authorized' : 'Not Authorized'));
        return $auth;
    }

    private function userCanAccessChannel($user, $channelName)
    {
        $recipient = explode('.', str_replace('private-chat.', '', $channelName));
        error_log('Parsed Recipient: ' . json_encode($recipient));

        if (count($recipient) !== 2) {
            error_log("Error: Invalid channel name format for {$channelName}");
            return false;
        }

        $isAuthorized = in_array($user->id, $recipient);
        error_log("Authorization check for user {$user->id}: " . ($isAuthorized ? 'Authorized' : 'Not Authorized'));

        return $isAuthorized;
    }
}
