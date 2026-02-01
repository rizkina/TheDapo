<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Rombel;
use Illuminate\Auth\Access\HandlesAuthorization;

class RombelPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Rombel');
    }

    public function view(AuthUser $authUser, Rombel $rombel): bool
    {
        return $authUser->can('View:Rombel');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Rombel');
    }

    public function update(AuthUser $authUser, Rombel $rombel): bool
    {
        return $authUser->can('Update:Rombel');
    }

    public function delete(AuthUser $authUser, Rombel $rombel): bool
    {
        return $authUser->can('Delete:Rombel');
    }

    public function restore(AuthUser $authUser, Rombel $rombel): bool
    {
        return $authUser->can('Restore:Rombel');
    }

    public function forceDelete(AuthUser $authUser, Rombel $rombel): bool
    {
        return $authUser->can('ForceDelete:Rombel');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Rombel');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Rombel');
    }

    public function replicate(AuthUser $authUser, Rombel $rombel): bool
    {
        return $authUser->can('Replicate:Rombel');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Rombel');
    }

}