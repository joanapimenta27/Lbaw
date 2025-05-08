<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Message;
use App\Events\MessageCreated;
use Illuminate\Support\Facades\Auth;
use App\Models\Group;
use App\Models\Post;


use Illuminate\Http\Request;

class messageController extends Controller
{
    public function index(Request $request, $recipient)
    {

        $groupName=$this->extractGroupName($recipient);
        if($groupName!=null){
            error_log($groupName);
            return $this->index_group( $request, $recipient,$groupName);
        }else{
            return $this->index_private($request, $recipient);
        }
    }
    

    public function index_private(Request $request, $recipient){

        $result = $this->getUsersByRecipient($recipient);
        if($result==null){
            abort(404);
        }
        $userA = $result['userA'];
        $userB = $result['userB'];

        $mainUser = Auth::id();
    
        if ($mainUser == $userA) {
            $recipient = (int)$userB;
        } elseif ($mainUser == $userB) {
            $recipient = (int)$userA;
        } else {
            abort(404, 'User mismatch');
        }
        $main = User::find($mainUser);
        $messagesCount = $request->input('messagesCount', 10);

        $otherUser = User::find($recipient);
        $totalMessages = Message::countMessagesBetweenUsers($userA, $userB);
        $messagesCount = min($messagesCount, $totalMessages);

        $messages = Message::getPrivateMessagebetween2Users($mainUser, $recipient, $messagesCount);
    
        return view('pages.Message', [
            'recipient' => $recipient,
            'messages' => $messages->items(),
            'otherUser' => $otherUser,
            'mainUser' => $main,
            'messagesCount' => $messagesCount,
            'maxMessages' => $totalMessages,
            'pusherKey' => env('PUSHER_APP_KEY'),
            'pusherCluster' => env('PUSHER_APP_CLUSTER'),
        ])->with('showFooter', false);

    }


    public function index_group(Request $request, $recipient,$groupName){

       
        $mainUser = Auth::id();
        $group =Group::findByName($groupName);
        $groupMember=$group->members();
        $main = User::find($mainUser);

        
        if (!$group || !$group->hasMember($main)) {
            abort(404);
        }
        $messagesCount = $request->input('messagesCount', 10);

        $totalMessages = Message::getTotalMessagesByGroup($group);
        $messagesCount = min($messagesCount, $totalMessages);

        $messages = Message::getMessagesByGroup($group,$messagesCount);
    
        return view('pages.Message', [
            'recipient' => $recipient,
            'messages' => $messages->items(),
            'mainUser' => $main,
            'group' =>$group,
            'messagesCount' => $messagesCount,
            'maxMessages' => $totalMessages,
            'pusherKey' => env('PUSHER_APP_KEY'),
            'pusherCluster' => env('PUSHER_APP_CLUSTER'),
        ])->with('showFooter', false);

    }


 public function broadcast(Request $request, $recipient)
{
   
    
    $requestData = json_encode($request->all(), JSON_PRETTY_PRINT);
    error_log("Request Data: \n" . $requestData);

    $group = $request->get('group');
   
    if( $group!=null){
        
        return $this->groupBroadcast($request, $recipient,$group);
    }else{
       

        return $this->privateBroadcast( $request, $recipient);

    }
    
}


public function privateBroadcast(Request $request, $recipient)  {
    $messageC = $request->get('message');
    $result = $this->getUsersByRecipient($recipient);
    $userA = $result['userA']; 
    $userB = $result['userB'];
    $main_user = Auth::user();
    if($main_user->id==$userA){
        $receiverId=(int)$userB;
    } elseif($main_user->id==$userB){
        $receiverId=(int)$userA;
    }
    $message = $this->validateMessage($request, $main_user->id, $receiverId,null,null);

    broadcast(new MessageCreated($message))->toOthers();
    $pic=$main_user->profile_picture ? asset('storage/' . $main_user->profile_picture) : asset('default-profile.png');
    error_log( $pic);

    return view('layouts.broadcast', ['message' => $message, 'user' => $main_user,'pic'=> $pic]);

    
}


public function groupBroadcast(Request $request, $recipient,$group)  {
    $groupValue=Group::findByName($group);
    $messageC = $request->get('message');
    $main_user = Auth::user();

    $message = $this->validateMessage($request,$main_user->id,null,$groupValue->id,null);
    $pic=$main_user->profile_picture ? asset('storage/' . $main_user->profile_picture) : asset('default-profile.png');
    error_log( $pic);

    broadcast(new MessageCreated($message,$groupValue))->toOthers();
    return view('layouts.broadcast', ['message' => $message, 'user' => $main_user,'pic'=>$pic]);

    
}


public function receive(Request $request)
{
    
    $message = $request->input('message');
    $sender =  $request->input('sender');
    $post_id=$request->input('postId');


    $pic = isset($sender['pic']) && $sender['pic'] 
        ? asset('storage/' . $sender['pic']) 
        : asset('default-profile.png');

    if ($post_id !== null) {
        $post = Post::find($post_id);
        if ($post) {
            return view('layouts.receivePost', [
                'post' => $post,
                'date' => $message['date'] ?? 'No date provided',
                'user' => $sender['name'] ?? 'Unknown User',
                'pic' => $pic,
            ]);


        } else {
            error_log('Error: Post not found with ID ' . $post_id);
            abort(404, 'Post not found');
        }
    } else {
        return view('layouts.receive', [
            'content' => $message['content'] ?? 'No content provided',
            'date' => $message['date'] ?? 'No date provided',
            'user' => $sender['name'] ?? 'Unknown User',
            'pic' => $pic,
        ]);
    }
}


public function validateMessage(Request $request, $senderId,$receiverId=null , $group_id = null,$postId=null)
{

    $validatedData = $request->validate([
        'message' => 'nullable|string|max:255',
    ]);

    $content = $validatedData['message'] ?? "post";

    $message = Message::create([
        'content' => $content,  
        'date' => now(),
        'sender_id' => $senderId, 
        'receiver_id' => $receiverId,// This can be null, so it's fine if not passed
        'group_id' => $group_id,  // This can be null, so it's fine if not passed
        'post_id' =>$postId,// This can be null
    ]);
 

    error_log("created");
    return $message;
}
    public function getUsersByRecipient($recipientName){
        $userNames=explode('.', $recipientName );
        if (count($userNames) === 2) {
            $userA = $userNames[0];
            $userB = $userNames[1];
            return ['userA' => $userA, 'userB' => $userB];
        }

        return null;
    }

    function extractGroupName($input) {
        error_log("value:".$input);
        if (str_starts_with($input, "group:")) {
            return substr($input, 6);
        }
    
        return null;
    }
    function sharePost(Request $request)  {
        
        $groupName = $request->input('group.name');
        $postId = $request->input('post.id');
        $main_user=Auth::id();
        $group=Group::findByName($groupName);

        
        if (!$group) {
            return response()->json(['success' => false, 'message' => 'Group not found']);
        }

        $message = $this->validateMessage($request,$main_user,null, $group->id, $postId);

        broadcast(new MessageCreated($message,$group))->toOthers();


        return response()->json(['success' => true, 'message' => 'Post shared successfully']);
    }
   

}
