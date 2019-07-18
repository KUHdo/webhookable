<?php
/**
 * Created by PhpStorm.
 * User: arnebartelt
 * Date: 16.04.19
 * Time: 13:47
 * Credits: https://github.com/laravel-notification-channels/webhook
 */

namespace App\Notifications\Messages;

use App\Repositories\WebHook\WebHookRepository;
use App\WebHook;

class DeprecatedRestHookMessage extends WebHookMessage
{
    private $webHookRepo;

    /**
     * RestHookMessage constructor.
     * @param WebHookRepository $webHookRepository
     */
    public function __construct(WebHookRepository $webHookRepository)
    {
        parent::__construct();
        $this->webHookRepo = $webHookRepository;
    }
    /**
     * Add a WebHook Model to Message
     *
     * @param \App\WebHook $hook
     * @return $this
     */
    public function webHook(WebHook $hook)
    {
        $this->webHook = $this->webHookRepo->replaceVars($hook, $this->data);

        return $this;
    }

}
