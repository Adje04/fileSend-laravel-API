<?php

namespace App\Repositories;

use App\Interfaces\InvitationInterface;
use App\Mail\InviteToGroupMail;
use App\Models\Group;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Support\Facades\Mail;


class InvitationRepository implements InvitationInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function sendInvitation(array $data)
    {

        $invitation = Invitation::create($data);

        if ($invitation) {

            Mail::to($data['email'])->send(new InviteToGroupMail($invitation));
        }
        return $invitation;
    }


    // public function processInvitation(array $data)
    // {
    //     $invitation = Invitation::where('email', $data['email'])->first();

    //     if ($invitation) {
    //         $this->addInvitedMember($invitation->group_id, $data);

    //         $invitation->delete();
    //     }
    // }


    // public function addInvitedMember(int $groupId,  array $data)
    // {
    //     $group = Group::findOrFail($groupId);
    //     $user = User::where('email', $data['email'])->first();
    //     if (!$user) {

    //         if (!$group->members()->where('user_id', $user->id)->exists()) {

    //             $group->members()->attach($user->id);
    //         }
    //     }
    // }



    public function processInvitation(array $data)
    {
        $invitation = Invitation::where('email', $data['email'])->first();

        if ($invitation) {
            $group = Group::findOrFail($invitation->group_id);
            $user = User::where('email', $data['email'])->first();

            if ($user) {

                $group->members()->attach($user->id);
            }

            $invitation->delete();
        }
    }
}
