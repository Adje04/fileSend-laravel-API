<?php

namespace App\Interfaces;

use App\Models\Group;
use App\Models\User;

interface GroupInterface
{
    public function create(array $data);
    public function index();

    public function show($id);

    public function addMember(int $id, array $data);

    public function createMember(array $data);

    public function notifyGroupMembers(Group $group, User $newUser);

    // public function sendInvitation(array $data);
}
