<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
   
    public function create()
    {
        return view('pages.createGroup');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:groups',
        ]);

      
        $group = Group::create([
            'name' => $request->input('name'),
            'owner_id' => Auth::id(),
        ]); 

        $group->addMember(User::find(Auth::id()));


        return redirect()->route('chatMenu.index')
                         ->with('success', 'Group created successfully!');
    }


    public function index_add_users($groupName){
        
        $group=Group::findByName($groupName);
        $this->checkAuthPremission( $group);

        return view('pages.addGroup', [
            'groupID' =>  $group->id, 
        ]);
    }

    public function  index_remove_users($groupName){

        $group=Group::findByName($groupName);
        checkAuthPremission( $group);

        return view('pages.removeUsersGroup', [
            'groupID' =>  $group->id, 
        ]);

    }
    public function index_edit_group($groupName)   {

        $group=Group::findByName($groupName);
        $this->checkAuthPremission( $group);

        return view('pages.editGroup', [
            'group' =>  $group, 
        ]);   
    }
   


    public function liveSearchAddUsers(Request $request)
    {
        $query = $request->get('search'); 
        $groupId=$request->get('group');
        $remove=$request->get('remove');
        $group = Group::find($groupId);
        $userId=Auth::id();
        $values = [];
        if($remove=="0"){
            if (empty($query)) {
                $values = $group->getFriendsNotInGroupAndNoInvite($groupId, $userId);
            } else {
                $values = $group->getFriendsNotInGroupAndNoInvite($groupId, $userId,$query);
            }

        }else{
            $values=$group->getMembersByQuery($query);
        }
        
        return response()->json($values);
    }


    public function removeMembers(Request $request){ 

        $userIds = $request->input('users');
        $groupId = $request->input('group');

        $group=Group::find($groupId);
        $this->checkAuthPremission( $group);

        if (empty($userIds)) {
            return response()->json(['success' => false, 'message' => 'No users selected']);
        }

        $users = User::whereIn('id', $userIds)->get();

        foreach ($users as $user) {

            if ($group->removeMember($user)) {
               error_log('User deleted: ' . $user->username);
           } else {
               error_log('Fail to delete: ' . $user->username);
           } 
       }
       return response()->json(['success' => true, 'message' => 'Users Deleted']);

   }

   public function leaveGroup($group)
{
    error_log("group leave");
    $groupv=Group::find( $group);
    $user = auth()->user();
    $groupv->leaveGroup($user);

    return redirect()->route('chatMenu.index')->with('status', 'You have left the group.');
}

    


    public function sendInvites(Request $request){

        $userIds = $request->input('users');
        $groupId = $request->input('group');
        $group=Group::find($groupId);
        $this->checkAuthPremission( $group);

     
        if (empty($userIds)) {
            return response()->json(['success' => false, 'message' => 'No users selected']);
        }
        $users = User::whereIn('id', $userIds)->get();

        foreach ($users as $user) {
            
             if ($group->addMember($user)) {
                error_log('Invite sent to user: ' . $user->username);
            } else {
                error_log('Failed to send invite to user: ' . $user->username);
            } 
        }
        return response()->json(['success' => true, 'message' => 'Invites sent successfully']);

    }

    public function updateGroup(Request $request, Group $group)
    {
        $request->validate([
            'group_name' => 'required|string|max:255',
        ]);
        $this->checkAuthPremission( $group);

        $group->name = $request->group_name;
        $group->save();
    
        return redirect()->route('messages.index', ['recipient' => 'group:' . $group->name])
            ->with('success', 'Group name updated successfully!');
    }
    

public function destroyGroup(Group $group)
{
    $this->checkAuthPremission( $group);
    $group->deleteGroup();
    return redirect()->route('chatMenu.index')->with('success', 'Group deleted successfully!');
}

public function checkAuthPremission(Group $group){
    $user=Auth::id();
    if($user!=$group->owner_id){
        abort(404);
    }

}

   
}
