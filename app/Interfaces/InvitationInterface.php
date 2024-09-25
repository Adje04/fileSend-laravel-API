<?php

namespace App\Interfaces;

use App\Models\User;

interface InvitationInterface
{
    public function sendInvitation(array $data);

    // public function processInvitation(User $user);

    // public function addInvitedMember(User $user, int $groupId);

    public function processInvitation(array $dataInvitation);

    // public function addInvitedMember(int $groupId, array $data);
}
