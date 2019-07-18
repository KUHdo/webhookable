<?php
/**
 * Created by PhpStorm.
 * User: arnebartelt
 * Date: 01.08.18
 * Time: 17:01
 */

namespace App\Repositories\WebHook;
use App\Commodity;
use App\Notifications\Messages\DeprecatedRestHookMessage;
use App\WebHook;
use Illuminate\Support\Collection;
use App\TradingMonth;


interface WebHookRepository
{
    public function urlHasVariables(WebHook $hook): bool;
    public function urlVariables(WebHook $hook): array;
    public function matchingEvents(String $event): Collection;
    public function fire(String $event, $data): void;
    public function replaceVars(WebHook $hook, $data): WebHook;
}