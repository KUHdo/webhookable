<?php
/**
 * Created by PhpStorm.
 * User: arnebartelt
 * Date: 20.03.19
 * Time: 08:39
 */

namespace KUHdo\Webhookable\Repositories;

use KUHdo\Webhookable\Notifications\WebHookNotification;
use KUHdo\Webhookable\WebHook;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class EloquentWebHook implements WebHookRepository
{
    /**
     * if there is an astrix in the event name it returns
     * all matching events
     * @param String $event
     * @return Collection webHooks
     */
    public function matchingEvents(String $event): Collection
    {
        $event = Str::contains($event, '*') ? explode('.', $event)[0]. '.%' : $event;
        return (new WebHook)->where('event', 'like', $event)
            ->get();
    }

    /**
     * fires a specific event to all web hooks
     * TODO write an test!
     * @param String $event
     * @param $data
     */
    public function fire(String $event, $data): void
    {
        $this->matchingEvents($event)
            ->each(function($hook) use ($data) {
                $hook->payload = $data;
                $hook->webhookable()
                    ->firstOrFail()
                    ->notify(new WebHookNotification($hook));
            });
    }

}