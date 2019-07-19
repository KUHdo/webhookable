<?php
/**
 * Created by PhpStorm.
 * User: arnebartelt
 * Date: 01.08.18
 * Time: 17:01
 */

namespace KUHdo\Webhookable\Repositories;
use Illuminate\Support\Collection;


interface WebHookRepository
{
    /**
     * @see \KUhdo\Webhookable\Repositories\EloquentWebHook::matchingEvents()
     * @param String $event
     * @return \Illuminate\Support\Collection
     */
    public function matchingEvents(String $event): Collection;

    /**
     * @see \KUhdo\Webhookable\Repositories\EloquentWebHook::fire()
     * @param String $event
     * @param $data
     */
    public function fire(String $event, $data): void;

}