<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\FileCategory;
use Illuminate\Auth\Access\HandlesAuthorization;

class FileCategoryPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:FileCategory');
    }

    public function view(AuthUser $authUser, FileCategory $fileCategory): bool
    {
        return $authUser->can('View:FileCategory');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:FileCategory');
    }

    public function update(AuthUser $authUser, FileCategory $fileCategory): bool
    {
        return $authUser->can('Update:FileCategory');
    }

    public function delete(AuthUser $authUser, FileCategory $fileCategory): bool
    {
        return $authUser->can('Delete:FileCategory');
    }

    public function restore(AuthUser $authUser, FileCategory $fileCategory): bool
    {
        return $authUser->can('Restore:FileCategory');
    }

    public function forceDelete(AuthUser $authUser, FileCategory $fileCategory): bool
    {
        return $authUser->can('ForceDelete:FileCategory');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:FileCategory');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:FileCategory');
    }

    public function replicate(AuthUser $authUser, FileCategory $fileCategory): bool
    {
        return $authUser->can('Replicate:FileCategory');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:FileCategory');
    }

}