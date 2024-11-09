<?php

namespace App\Policies;

use App\Enums\StatusEnum;
use App\Enums\StatusFoEnum;
use App\Models\Military;
use App\Models\User;
use App\Models\Fo;
use Illuminate\Auth\Access\HandlesAuthorization;

class FoPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_fo');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Fo $fo): bool
    {
        return $user->can('view_fo');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_fo');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Fo $fo): bool
    {
        return $user->can('update_fo');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Fo $fo): bool
    {
        if ($user->can('delete_fo')) {
            return true;
        }

        if ($fo->status->value !== StatusFoEnum::EM_ANDAMENTO->value) {
            return false;
        }

        $issuer = Military::firstWhere('id', $fo->issuer);

        return $user->rg === $issuer->rg;
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_fo');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Fo $fo): bool
    {
        return $user->can('force_delete_fo');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_fo');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Fo $fo): bool
    {
        return $user->can('restore_fo');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_fo');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Fo $fo): bool
    {
        return $user->can('replicate_fo');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_fo');
    }
}
