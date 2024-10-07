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
        $user = Auth::user();
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

           $fileData = $this->fileInterface->upload($data);

            DB::commit();
            return ApiResponse::sendResponse(true, $fileData , 'Opération effectuée.', 201);
        } catch (\Throwable $th) {
            return $th;
            return ApiResponse::rollback($th);
        }
    }

     
    public function getFilesForGroup($groupId)
    {
        try {
            DB::beginTransaction();

            $files = $this->fileInterface->getFilesByGroupId($groupId)->load('user');

            DB::commit();
            return ApiResponse::sendResponse(true, $files, 'Liste des fichiers récupérée avec succès.', 200);
        } catch (\Throwable $th) {
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









