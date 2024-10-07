<?php

namespace App\Interfaces;

use App\Models\Group;
use App\Models\User;

interface GroupInterface
{
    // public function create(array $data);
    public function create(array $data, $creatorEmail);
    public function index();

    public function getGroupByUser();

    public function addMember(int $id, array $data);

    public function createMember(array $data);

    public function notifyGroupMembers(Group $group, User $newUser);

    // public function sendInvitation(array $data);
}
