<?php

namespace KUHdo\Webhookable\Policies;

use Illuminate\Foundation\Auth\User;
use Illuminate\Contracts\Auth\Authenticatable;
use KUHdo\Webhookable\WebHook;
use Illuminate\Auth\Access\HandlesAuthorization;

class WebHookPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the web hook.
     *
     * @param  \App\User  $user
     * @param  \App\WebHook  $webHook
     * @return mixed
     */
    public function view(User $user, WebHook $webHook)
    {
        if($webHookUser = $webHook->webhookable()->first()) {
            return $user->is($webHookUser);
        }
        return false;
    }

    /**
     * Determine whether the user can create web hooks.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        // TODO: check permission
        return true;
    }

    /**
     * Determine whether the user can update the web hook.
     *
     * @param  \App\User  $user
     * @param  \App\WebHook  $webHook
     * @return mixed
     */
    public function update(User $user, WebHook $webHook)
    {
        if($webHookUser = $webHook->webhookable()->first()) {
            return $user->is($webHookUser);
        }
        return false;
    }

    /**
     * Determine whether the user can delete the web hook.
     *
     * @param  \App\User  $user
     * @param  \App\WebHook  $webHook
     * @return mixed
     */
    public function delete(User $user, WebHook $webHook)
    {
        if($webHookUser = $webHook->webhookable()->first()) {
            return $user->is($webHookUser);
        }
        return false;
    }

}
