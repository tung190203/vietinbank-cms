<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Post;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any posts.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    public function view(User $user, Post $post)
    {
        //
    }

    public function create(User $user)
    {
        //
    }

    public function update(User $user, Post $post)
    {
        //
    }

    public function delete(User $user, Post $post)
    {
        //
    }

    public function restore(User $user, Post $post)
    {
        //
    }

    public function forceDelete(User $user, Post $post)
    {
        //
    }
}
