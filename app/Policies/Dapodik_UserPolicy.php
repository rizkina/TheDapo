<?php

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Auth\Access\HandlesAuthorization;

class Dapodik_UserPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:DapodikUser');
    }

    public function view(AuthUser $authUser): bool
    {
        return $authUser->can('View:DapodikUser');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:DapodikUser');
    }

    public function update(AuthUser $authUser): bool
    {
        return $authUser->can('Update:DapodikUser');
    }

    public function delete(AuthUser $authUser): bool
    {
        return $authUser->can('Delete:DapodikUser');
    }

    public function restore(AuthUser $authUser): bool
    {
        return $authUser->can('Restore:DapodikUser');
    }

    public function forceDelete(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDelete:DapodikUser');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:DapodikUser');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:DapodikUser');
    }

    public function replicate(AuthUser $authUser): bool
    {
        return $authUser->can('Replicate:DapodikUser');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:DapodikUser');
    }

}