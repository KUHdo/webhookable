<?php
/**
 * Created by PhpStorm.
 * User: arnebartelt
 * Date: 26.04.19
 * Time: 10:19
 */

namespace KUHdo\Webhookable\Contracts;
use Illuminate\Database\Eloquent\Relations\MorphMany;

interface Webhookable
{
    /**
     * returns the pre-shared secret for the
     * web hook signature
     * @return string
     */
    public function getSigningKey() : string;

    /**
     * Get all of the access tokens for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function webHooks() : MorphMany;
}