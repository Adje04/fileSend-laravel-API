<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest\LoginRequest;
use App\Http\Requests\AuthRequest\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Interfaces\AuthInterface;
use App\Interfaces\InvitationInterface;
use App\Models\Invitation;
use App\Models\User;
use App\Responses\ApiResponse;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    private AuthInterface $authInterface;
    private InvitationInterface $authInvitationInterface;

    public function __construct(AuthInterface $authInterface, InvitationInterface $authInvitationInterface)
    {
        $this->authInterface = $authInterface;
        $this->authInvitationInterface = $authInvitationInterface;
    }


    public function register(RegisterRequest $registerRequest)
    {
        $data = [
            'name' => $registerRequest->name,
            'email' => $registerRequest->email,
            'password' => $registerRequest->password,
            'passwordConfirm' => $registerRequest->passwordConfirm,
        ];

        DB::beginTransaction();
        try {
            $user = $this->authInterface->register($data);

            $this->authInvitationInterface->processInvitation($data);

            DB::commit();
            // [new UserResource($user)] la data qu'on envoie
            return ApiResponse::sendResponse(true, [new UserResource($user)], 'Opération effectuée.', 201);
        } catch (\Throwable $th) {

            return ApiResponse::rollback($th);
        }
    }





    public function login(LoginRequest $loginRequest)
    {
        $data = [
            'email' => $loginRequest->email,
            'password' => $loginRequest->password,
        ];

        DB::beginTransaction();
        try {
            $user = $this->authInterface->login($data);

            DB::commit();

            return ApiResponse::sendResponse(
                true,
                [new UserResource($user)],
                'Vous êtes connecté.',
                $user ? 200 : 401
            );
        } catch (\Throwable $th) {

            return ApiResponse::rollback($th);
        }
    }

    public function logout()
    {
        $user = User::find(auth()->user()->getAuthIdentifier());
        $user->tokens()->delete();

        return ApiResponse::sendResponse(
            true,
            [],
            'utilisateur déconnecté',
            200
        );
    }
}
