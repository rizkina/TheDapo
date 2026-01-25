<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\DapodikConf;
use Illuminate\Auth\Access\HandlesAuthorization;

class DapodikConfPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:DapodikConf');
    }

    public function view(AuthUser $authUser, DapodikConf $dapodikConf): bool
    {
        return $authUser->can('View:DapodikConf');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:DapodikConf');
    }

    public function update(AuthUser $authUser, DapodikConf $dapodikConf): bool
    {
        return $authUser->can('Update:DapodikConf');
    }

    public function delete(AuthUser $authUser, DapodikConf $dapodikConf): bool
    {
        return $authUser->can('Delete:DapodikConf');
    }

    public function restore(AuthUser $authUser, DapodikConf $dapodikConf): bool
    {
        return $authUser->can('Restore:DapodikConf');
    }

    public function forceDelete(AuthUser $authUser, DapodikConf $dapodikConf): bool
    {
        return $authUser->can('ForceDelete:DapodikConf');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:DapodikConf');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:DapodikConf');
    }

    public function replicate(AuthUser $authUser, DapodikConf $dapodikConf): bool
    {
        return $authUser->can('Replicate:DapodikConf');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:DapodikConf');
    }

}