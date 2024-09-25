<?php

namespace App\Http\Controllers;

use App\Http\Requests\GroupRequest;
use App\Http\Requests\MemberRequest;
use App\Http\Resources\GroupResource;
use App\Interfaces\GroupInterface;
use App\Interfaces\InvitationInterface;
use App\Responses\ApiResponse;
use Illuminate\Support\Facades\DB;

class GroupController extends Controller
{
    private GroupInterface $groupInterface;
    private InvitationInterface $invitationInterface;

    public function __construct(GroupInterface $groupInterface, InvitationInterface $invitationInterface)
    {
        $this->groupInterface = $groupInterface;
        $this->invitationInterface = $invitationInterface;
    }


    public function index()
    {
        try {
            //   $groups = Group::with('members')->get();
            //   return GroupResource::collection($groups);
            $groups = $this->groupInterface->index();
            return ApiResponse::sendResponse(
                true,
                GroupResource::collection($groups),
                'Groupes récupérés avec succès',
                200
            );
        } catch (\Throwable $th) {
            return ApiResponse::rollback($th);
        }
    }

    public function registerGroup(GroupRequest $groupRequest)
    {
        $data = [
            'name' => $groupRequest->name,
            'description' => $groupRequest->description,
            'user_id' => auth()->id(),
        ];

        DB::beginTransaction();
        try {
            $group = $this->groupInterface->create($data);

            DB::commit();
            // [new groupResource($group)] la data qu'on envoie
            return ApiResponse::sendResponse(true, [new GroupResource($group)], 'Groupe créé avec succès', 201);
        } catch (\Throwable $th) {
            // return $th;
            return ApiResponse::rollback($th);
        }
    }




    public function addMember(MemberRequest $memberRequest, $id)
    {
        DB::beginTransaction();
        try {
            $data = ['email' => $memberRequest->email,];

            $group = $this->groupInterface->addMember($id, $data);

            if (isset($group['memberExist'])) {
                return ApiResponse::sendResponse(
                    false,
                    [],
                    'Email déjà utilisé',
                    409 // Code de conflit pour indiquer que l'utilisateur existe déjà
                );
            }

       
            if (!$group) {
                $data = [
                    'group_id' => $id, 
                    'email' => $memberRequest->email,
                ];

                
               $this->invitationInterface->sendInvitation($data);

                DB::commit();
                return ApiResponse::sendResponse(
                    false,
                    [],
                    'Pas encore inscrit! Une invitation vous a été envoyée',
                    201
                );
            }

            DB::commit();
            return ApiResponse::sendResponse(
                true,
                [new GroupResource($group)],
                'Membre ajouté avec succès',
                201
            );



        } catch (\Throwable $th) {

            return $th;
            return ApiResponse::rollback($th);
        }
    }
}






     // if ($group) {
            //     DB::commit();
            //     return ApiResponse::sendResponse(
            //         true,
            //         [new GroupResource($group)],
            //         'Membre ajouté avec succès',
            //         201
            //     );
            // } else {

            //     $data = [
            //         'group_id' => $id,
            //         'email' => $memberRequest->email,
            //     ];
            //     DB::beginTransaction();
            //     try {
            //         $invitation = $this->groupInterface->sendInvitation($data);

            //         DB::commit();

            //         return ApiResponse::sendResponse(
            //             true,
            //             $invitation,
            //             'Pas encore inscrit! une invitation vous a eté envoyé',
            //             201
            //         );
            //     } catch (\Throwable $th) {
            //         return $th;
            //         return ApiResponse::rollback($th);
            //     }
            // }





 /*     public function addMember(MemberRequest $memberRequest, $id)
    {
        DB::beginTransaction();
        try {
            $email = $memberRequest->email;

            $user = User::where('email', $email)->first();

            $group = $this->groupInterface->find($id);

            if ($user) {

                $group->members()->attach($user->id);
            } else {

                return ApiResponse::sendResponse(
                    false,
                    [],
                    'l\'utilisateur n\'est pas inscrit',
                    201
                );
            }
            DB::commit();

            return ApiResponse::sendResponse(
                true,
                [new GroupResource($group)],
                'Membre ajouté ou invitation envoyée',
                201
            );
        } catch (\Throwable $th) {
            DB::rollback();
            return ApiResponse::rollback($th);
        }
    }



     L'utilisateur n'est pas inscrit, on envoie une invitation
                $invitation = Invitation::create([
                    'email' => $request->email,
                    'group_id' => $id,
                    'token' => Str::random(32),
                ]);
                 Envoyer un email d'invitation
                Mail::to($request->email)->send(new \App\Mail\InviteToGroup($invitation));
                 return ApiResponse::sendResponse(
                     true,
                     [],
                     'Invitation envoyée',
                     201

  */