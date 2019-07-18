<?php
/**
 * Created by PhpStorm.
 * User: arnebartelt
 * Date: 20.03.19
 * Time: 08:39
 */

namespace App\Repositories\WebHook;


use App\Notifications\WebHookNotification;
use App\WebHook;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class EloquentWebHook implements WebHookRepository
{
    /**
     * deprecated
     * @param WebHook $hook
     * @return bool
     */
    public function urlHasVariables(WebHook $hook): bool
    {
        return Str::contains($hook->url, ['{', '}', '{}']);
    }

    /**
     * deprecated
     * @param WebHook $hook
     * @return array variables
     */
    public function urlVariables(WebHook $hook): array
    {
        if($this->urlHasVariables($hook)) {
            return collect(explode('{', $hook->url))->filter(function($var) {
                return Str::endsWith($var, ['}', '}/']);
            })->transform(function($var) {
                return Str::before($var, '}');
            })->unique()->toArray();
        }
        return [];
    }


    /**
     * deprecated
     * @param WebHook $hook
     * @param Model| Arrayable $data
     * @return WebHook
     * @throws \ErrorException
     */
    public function replaceVars(WebHook $hook, $data): WebHook
    {
        $vars = $this->urlVariables($hook);
        foreach($vars as $var) {
            if($data instanceof Model) {
                if(array_key_exists($var, $data->attributesToArray())) {
                    $hook->url = str_replace('{'.$var . '}', urlencode($data->$var), $hook->url);
                } else {
                    throw new \ErrorException("Undefined property on model");
                }
            } else {
                if(array_key_exists($var, (array) $data)) {
                    $hook->url = str_replace('{'.$var . '}', urlencode($data[$var]), $hook->url);
                } else {
                    throw new \ErrorException("Could not replace var, key not found!");
                }
            }
        }
        return $hook;
    }

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