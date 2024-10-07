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
use Illuminate\Support\Facades\Auth;
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

    public function create(array $data, $creatorEmail)
    {
        $group = Group::create($data);
        $this->addMember($group->id, ['email' => $creatorEmail]);
        return  $group;
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

    public function getGroupByUser()
    {

        $authUser = Auth::id();

        return Group::withWhereHas('members', fn($query) =>
        $query->where('user_id', $authUser))
            ->get();
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
}
