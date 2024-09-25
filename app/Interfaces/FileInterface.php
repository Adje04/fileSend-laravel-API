<?php

namespace App\Interfaces;

interface FileInterface
{
  
    public function upload(array $data);
     public function download(array $data);
     public function findFileByIdAndGroup($groupId, $fileId);
     public function delete($fileId);
    // public function delete($filename);
    // public function listFiles();
    // public function getInfo($filename);
    // public function search($keywords);
    // public function getSize($filename);
    // public function getPermissions($filename);
    // public function setPermissions($filename, $permissions);
    // public function move($filename, $newPath);
    // public function copy($filename, $newPath);
    // public function rename($filename, $newName);
    // public function createFolder($folderName);
    // public function deleteFolder($folderName);
    // public function listFolderContents($folderName);
    // public function getFolderSize($folderName);
    // public function getFolderPermissions($folderName);
    // public function setFolderPermissions($folderName, $permissions);
    // public function moveFolder($folderName, $newPath);
    // public function copyFolder($folderName, $newPath);
    // public function renameFolder($folderName, $newName);
}
