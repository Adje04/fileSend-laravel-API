<?php

namespace App\Interfaces;

interface FileInterface
{
  
    public function upload(array $data);
     public function download(array $data);
     public function findFileByIdAndGroup($groupId, $fileId);
     public function delete($fileId);
     public function getFilesByGroupId($groupId);
   

   }