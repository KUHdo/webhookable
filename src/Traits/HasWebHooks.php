<?php
/**
 * Created by PhpStorm.
 * User: arnebartelt
 * Date: 14.03.19
 * Time: 13:55
 */

namespace App\Traits;


use App\WebHook;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasWebHooks
{
    /**
     * get shared secret for request singing
     * may overwrite the method if you're not using passport
     * @return string
     */
    public function getSigningKey() : string
    {
        return $this->tokens()->firstOrFail()->id;
    }

    /**
     * Get all of the access tokens for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\morphMany
     */
    public function webHooks() : morphMany
    {
        return $this->morphMany(WebHook::class, 'webhookable');
    }
}