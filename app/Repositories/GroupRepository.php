<?php

namespace App\Repositories;

use App\Interfaces\GroupInterface;
use App\Mail\InviteToGroupMail;
use App\Mail\NotifyGroupMemberMail;
use App\Mail\RegisterUserAddedMail;
use App\Models\Group;
use App\Models\Invitation;
use App\Models\Member;
use App\Models\User;
use App\Responses\ApiResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Str;
use function PHPUnit\Framework\assertNotFalse;

class GroupRepository implements GroupInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    public function index()
    {
        return Group::all();
    }

    public function create(array $data)
    {
        return  Group::create($data);
    }

    //ici l'id du groupe
    public function show($id)
    {
        return Group::findOrFail($id);
    }
    public function createMember(array $data)
    {
        return  Member::create($data);
    }

    public function addMember(int $id, array $data)
    {

        $group = Group::findOrFail($id);
        $user = User::where('email', $data['email'])->first();

        if ($user) {

            // Si l'utilisateur existe déjà, on ne le rajoute pas à nouveau
            if ($group->members()->where('user_id', $user->id)->exists()) {
                return ['memberExist' => 'user already exists'];
            }

            // Sinon, on l'ajoute au groupe et on envoie un e-mail au nouvel arrivé

            $group->members()->attach($user->id);

            Mail::to($data['email'])->send(new RegisterUserAddedMail($group, $user));
            //notifier tout le groupe de l'arrivé d'un nouveau membre
            $this->notifyGroupMembers($group, $user);
            return [
                'group' => $group,
                'user' => $user
            ];
        } else {
            return false;
        }
    }

    public function notifyGroupMembers(Group $group, User $newUser)
    {
        // Récupérer tous les membres actuels du groupe
        $members = $group->members()->where('users.id', '!=', $newUser->id)->get();
        // Envoyer un e-mail à chaque membre pour les informer du nouvel ajout
        foreach ($members as $member) {
            Mail::to($member->email)->send(new NotifyGroupMemberMail($group, $newUser));
        }
    }


    // public function sendInvitation(array $data)
    // {

    //     Invitation::create($data);

    //     // Envoyer l'e-mail d'invitation
    //     Mail::to($data['email'])->send(new InviteToGroupMail($data['group'], (object) ['email' => $data['email']],));
    // }
}

/*
public function addMember(int $groupId, string $email)
{
    $group = Group::findOrFail($groupId);
    $user = User::where('email', $email)->first();

    if ($user) {
        // Si l'utilisateur existe déjà, on l'ajoute au groupe
        $group->members()->attach($user->id);

        // Envoyer un e-mail à l'utilisateur ajouté
        Mail::to($user->email)->send(new GroupNotificationMail($group, $user, 'user_added'));

        // Envoyer un e-mail à tous les autres membres du groupe
        $this->notifyGroupMembers($group, $user);
    } else {
        // Si l'utilisateur n'existe pas, on envoie une invitation
        $this->sendInvitationToNonRegisteredUser($group, $email);
    }
}

private function notifyGroupMembers(Group $group, User $newUser)
{
    $members = $group->members()->where('id', '!=', $newUser->id)->get();

    foreach ($members as $member) {
        Mail::to($member->email)->send(new GroupNotificationMail($group, $member));
    }
}

private function sendInvitationToNonRegisteredUser(Group $group, string $email)
{
    $token = Str::random(32);
    Invitation::create([
        'group_id' => $group->id,
        'email' => $email,
        'token' => $token,
        'expires_at' => Carbon::now()->addDays(7),
    ]);

    // Envoyer l'e-mail d'invitation
    Mail::to($email)->send(new GroupNotificationMail($group, (object) ['name' => $email], 'invite', $token));
}
*/



















 /*namespace App\Repositories;

use App\Interfaces\GroupInterface;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class GroupRepository implements GroupInterface
{
    public function create(array $data)
    {
        return Group::create($data);
    }

    public function addMember(int $id, string $email)
    {
        $group = Group::findOrFail($id);
        $user = User::where('email', $email)->first();

        if ($user) {
            // Si l'utilisateur existe déjà, on l'ajoute au groupe
            $group->members()->attach($user->id);
        } else {
            // Si l'utilisateur n'existe pas, on lui envoie une invitation
            $this->sendInvitationEmail($group, $email);
        }
    }

    private function sendInvitationEmail(Group $group, string $email)
    {
        // Logique pour envoyer un e-mail d'invitation
        Mail::to($email)->send(new \App\Mail\InviteToGroup($group));
    }
   
}*/