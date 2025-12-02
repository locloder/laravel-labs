<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\User;

Broadcast::channel('project.{id}', function (User $user, $id) {
    $isOwner = $user->project_owner()->where('id', $id)->exists();
    $isMember = $user->projects()->where('projects.id', $id)->exists();

    return $isOwner || $isMember;

}, ['guards' => ['web', 'sanctum']]);
