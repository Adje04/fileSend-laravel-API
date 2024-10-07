<?php

namespace App\Http\Controllers;

use App\Http\Requests\GroupRequest;
use App\Http\Requests\MemberRequest;
use App\Http\Resources\GroupResource;
use App\Interfaces\GroupInterface;
use App\Interfaces\InvitationInterface;
use App\Responses\ApiResponse;
use Illuminate\Support\Facades\Auth;
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
    public function getGroupByUser() {
        try {
            //  $groups = Group::where('user_id', auth()->id())->get();
            $groupByUser = $this->groupInterface->getGroupByUser();
    
            return ApiResponse::sendResponse(
                true,
                GroupResource::collection($groupByUser),
                'Groupes récupérés avec succès pour l’utilisateur connecté',
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
            'user_id' => Auth::id(),

        ];

        DB::beginTransaction();
        try {
            $group = $this->groupInterface->create($data, Auth::user()->email);

            DB::commit();
           
            return ApiResponse::sendResponse(true, [new GroupResource($group)], 'Groupe créé avec succès', 201);
        } catch (\Throwable $th) {
           
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
                    409
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