<?php

namespace KUHdo\Webhookable\Policies;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use Illuminate\Contracts\Auth\Authenticatable;
use KUHdo\Webhookable\Contracts\Webhookable;
use KUHdo\Webhookable\WebHook;
use Illuminate\Auth\Access\HandlesAuthorization;

class WebHookPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the web hook.
     *
     * @param \KUHdo\Webhookable\Contracts\Webhookable $model
     * @param \KUHdo\Webhookable\WebHook $webHook
     * @return mixed
     */
    public function view(Model $model, WebHook $webHook)
    {
        if(! $model instanceof Webhookable) {
            return false;
        }
        if($webHookUser = $webHook->webhookable()->first()) {
            return $model->is($webHookUser);
        }
        return false;
    }

    /**
     * Determine whether the user can create web hooks.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return mixed
     */
    public function create(Model $model)
    {
        if($model instanceof Webhookable) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Determine whether the user can update the web hook.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param \KUHdo\Webhookable\WebHook $webHook
     * @return mixed
     */
    public function update(Model $model, WebHook $webHook)
    {
        if(! $model instanceof Webhookable) {
            return false;
        }
        if($webHookUser = $webHook->webhookable()->first()) {
            return $model->is($webHookUser);
        }
        return false;
    }

    /**
     * Determine whether the user can delete the web hook.
     *
     * @param \KUHdo\Webhookable\Contracts\Webhookable $model
     * @param \KUHdo\Webhookable\WebHook $webHook
     * @return mixed
     */
    public function delete(Webhookable $model, WebHook $webHook)
    {
        if(! $model instanceof Webhookable) {
            return false;
        }
        if($webHookUser = $webHook->webhookable()->first()) {
            return $model->is($webHookUser);
        }
        return false;
    }

}
