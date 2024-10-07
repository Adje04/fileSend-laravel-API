<?php

namespace App\Interfaces;

use App\Models\User;

interface InvitationInterface
{
    public function sendInvitation(array $data);

    public function processInvitation(array $dataInvitation);

   
}
