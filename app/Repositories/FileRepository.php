<?php

namespace App\Repositories;

use App\Interfaces\FileInterface;
use App\Mail\NotifyFileUploadMail;
use App\Models\File;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class FileRepository implements FileInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    public function upload(array $data)
    {
        $fileUploaded =  File::create($data);
        $group = Group::findOrFail($data['group_id']);

        // Inclure les informations de l'utilisateur qui a téléchargé le fichier
        $fileUploaded->user = User::findOrFail($data['user_id']);
        
        $this->notifyGroupMembers($group,  $fileUploaded);

        return $fileUploaded;
    }

    // Méthode pour trouver un fichier par son ID et le groupe auquel il appartient
    public function findFileByIdAndGroup($groupId, $fileId)
    {
        return File::where('group_id', $groupId)->where('id', $fileId)->first();
    }


    public function getFilesByGroupId($groupId)
    {
        return File::where('group_id', $groupId)->get();
    }


    public function download(array $data)
    {
        // Vérifier si l'utilisateur connecté est membre du groupe
        $group = Group::with('members')->findOrFail($data['group_id']);


        if (!$group->members->contains($data['user_id'])) {
            return null;
        }

        // Vérifier si le fichier existe dans le groupe
        $file = File::where('group_id', $data['group_id'])->findOrFail($data['file_id']);

        // Vérifier si le fichier existe sur le serveur
        if (!Storage::disk('public')->exists($file->path)) {
            return false; // We'll handle the error in the controller
        }

        return $file;
    }


    public function delete($fileId)
    {
        // Rechercher le fichier à supprimer
        $file = File::find($fileId);

        if (!$file) {
            return ['success' => false, 'message' => 'Fichier non trouvé.'];
        }

        // Supprimer le fichier du stockage
        if (Storage::disk('public')->exists($file->path)) {
            Storage::disk('public')->delete($file->path);
        }

        // Supprimer l'enregistrement dans la base de données
        $file->delete();

        return ['success' => true, 'message' => 'Fichier supprimé avec succès.'];
    }


    public function notifyGroupMembers(Group $group, File $file)
    {
        // Récupérer tous les membres actuels du groupe
        $members = $group->members()->where('users.id', '!=', $file->id)->get();
        // Envoyer un e-mail à chaque membre pour les informer du nouvel ajout
        foreach ($members as $member) {
            Mail::to($member->email)->send(new NotifyFileUploadMail($group, $file));
        }
    }
}












/*
public function findFileById($groupId, $fileId)
{
    return File::where('group_id', $groupId)->findOrFail($fileId);
}

public function checkIfUserIsGroupMember($groupId, $userId)
{
    $group = Group::with('members')->findOrFail($groupId);
    return $group->members->contains('id', $userId);
}


public function downloadFile($file)
{
    if (!Storage::disk('public')->exists($file->path)) {
        return null;
    }

    return Storage::disk('public')->download($file->path, $file->original_name);
}
*/