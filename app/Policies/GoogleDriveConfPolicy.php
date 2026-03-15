<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\GoogleDriveConf;
use Illuminate\Auth\Access\HandlesAuthorization;

class GoogleDriveConfPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:GoogleDriveConf');
    }

    public function view(AuthUser $authUser, GoogleDriveConf $googleDriveConf): bool
    {
        return $authUser->can('View:GoogleDriveConf');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:GoogleDriveConf');
    }

    public function update(AuthUser $authUser, GoogleDriveConf $googleDriveConf): bool
    {
        return $authUser->can('Update:GoogleDriveConf');
    }

    public function delete(AuthUser $authUser, GoogleDriveConf $googleDriveConf): bool
    {
        return $authUser->can('Delete:GoogleDriveConf');
    }

    public function restore(AuthUser $authUser, GoogleDriveConf $googleDriveConf): bool
    {
        return $authUser->can('Restore:GoogleDriveConf');
    }

    public function forceDelete(AuthUser $authUser, GoogleDriveConf $googleDriveConf): bool
    {
        return $authUser->can('ForceDelete:GoogleDriveConf');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:GoogleDriveConf');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:GoogleDriveConf');
    }

    public function replicate(AuthUser $authUser, GoogleDriveConf $googleDriveConf): bool
    {
        return $authUser->can('Replicate:GoogleDriveConf');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:GoogleDriveConf');
    }

}