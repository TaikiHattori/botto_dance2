<?php

namespace App\Policies;

use App\Models\Upload;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class UploadPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Upload $upload): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // 'new' ロールを持つユーザーにはアップロードを許可しない
        //return $user->role !== 'new';⇐不要（なくてもUP制限できた）

        //----------------------------------------------------------------
        // 特定のメールアドレスを持つユーザーにのみアップロードを許可
         $allowedEmail = 'kiheitai@example.com'; 

         return $user->email === $allowedEmail;
        //return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Upload $upload): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Upload $upload): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Upload $upload): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Upload $upload): bool
    {
        //
    }
}
