<?php

namespace App\Http\Controllers;

use App\Http\Requests\fileRequest;
use App\Interfaces\FileInterface;
use App\Models\File;
use App\Models\Group;
use App\Responses\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{

    private FileInterface $fileInterface;


    public function __construct(FileInterface  $fileInterface)
    {
        $this->fileInterface = $fileInterface;
    }
    public function upload(fileRequest $fileRequest, $id)
    {

        $file = $fileRequest->file('file');
        $path = $file->store('groupes_files', 'public');

        $data = [
            'name' => $file->getClientOriginalName(),
            'path' => $path,
            'size' => $file->getSize(),
            'type' => $file->getClientMimeType(),
            'user_id' => Auth::id(),
            'group_id' => $id,
        ];
        try {
            DB::beginTransaction();

            $this->fileInterface->upload($data);

            DB::commit();
            return ApiResponse::sendResponse(true, $data, 'Opération effectuée.', 201);
        } catch (\Throwable $th) {
return $th;
            return ApiResponse::rollback($th);
        }
    }


    public function download($groupId, $fileId)
    {
        $data = [
            'group_id' => $groupId,
            'file_id' => $fileId,
            'user_id' => Auth::id(),
        ];

        try {
            DB::beginTransaction();

            $file = $this->fileInterface->download($data);

            // Check if the user is unauthorized to access the group
            if (is_null($file)) {

                DB::rollBack();
                return ApiResponse::sendResponse(false, $data, 'non autorisé ; vous n\'êtes pas membre de ce groupe.', 403);
            }

            // verifiier si le fichier existe
            if (!$file) {
                DB::rollBack();
                return ApiResponse::sendResponse(false, $data, 'fichier non trouvé.', 404);
            }

            DB::commit();

            return Storage::disk('public')->download($file->path, $file->name);
        } catch (\Throwable $th) {

            return ApiResponse::rollback($th);
        }
    }



    public function delete($groupId, $fileId)
    {


        try {
            // Vérifier si le fichier appartient au groupe
            DB::beginTransaction();

            $file = $this->fileInterface->findFileByIdAndGroup($groupId, $fileId);

            if (!$file) {

                return ApiResponse::sendResponse(
                    false,
                    [],
                    'Fichier non trouvé dans ce groupe.',
                    404
                );
            };

            // Suppression du fichier
            $response = $this->fileInterface->delete($fileId);

            DB::commit();
            return ApiResponse::sendResponse(
                true,
                $response,
                'Fichier suprimmé avec succès !',
                200
            );
        } catch (\Throwable $th) {
            return ApiResponse::rollback($th);
        }
    }
}










/*
        $path = Storage::putFile('uploads', $file);
        'file_path' => asset('storage/' . $path),
        'file_name' => $file->getClientOriginalName(),
        'file_url' => route('download', ['file' => $path]),
        'file_created_at' => $file->getCreatedAt()->format('Y-m-d H:i:s'),
        'file_updated_at' => $file->getUpdatedAt()->format('Y-m-d H:i:s'),




        public function download($groupId, $fileId)
        {
            // Check if the authenticated user is a member of the group
            $group = Group::with('members')->findOrFail($groupId);
            if (!$group->members->contains(auth()->user())) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
    
            // The rest of the logic for downloading the file
            $file = File::where('group_id', $groupId)->findOrFail($fileId);
    
            if (!Storage::disk('public')->exists($file->path)) {
                return response()->json(['message' => 'File not found.'], 404);
            }
    
            return Storage::disk('public')->download($file->path, $file->original_name);
        }
    
    
    
        public function download($groupId, $fileId)
        {
            // Vérification si l'utilisateur est un membre du groupe
            if (!$this->fileInterface->checkIfUserIsGroupMember($groupId, auth()->id())) {
                return response()->json(['message' => 'Non autorisé'], 403);
            }
    
            // Récupération du fichier par ID
            $file = $this->fileInterface->findFileById($groupId, $fileId);
    
            // Télécharger le fichier si trouvé, sinon retourner un message d'erreur
            $downloadResponse = $this->fileInterface->downloadFile($file);
            if (!$downloadResponse) {
                return response()->json(['message' => 'Fichier introuvable'], 404);
            }
    
            return $downloadResponse;
        } */