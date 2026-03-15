<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Ptk;
use Illuminate\Auth\Access\HandlesAuthorization;

class PtkPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Ptk');
    }

    public function view(AuthUser $authUser, Ptk $ptk): bool
    {
        return $authUser->can('View:Ptk');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Ptk');
    }

    public function update(AuthUser $authUser, Ptk $ptk): bool
    {
        return $authUser->can('Update:Ptk');
    }

    public function delete(AuthUser $authUser, Ptk $ptk): bool
    {
        return $authUser->can('Delete:Ptk');
    }

    public function restore(AuthUser $authUser, Ptk $ptk): bool
    {
        return $authUser->can('Restore:Ptk');
    }

    public function forceDelete(AuthUser $authUser, Ptk $ptk): bool
    {
        return $authUser->can('ForceDelete:Ptk');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Ptk');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Ptk');
    }

    public function replicate(AuthUser $authUser, Ptk $ptk): bool
    {
        return $authUser->can('Replicate:Ptk');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Ptk');
    }

}