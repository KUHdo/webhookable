<?php

namespace KUHdo\Webhookable\Notifications;

use KUHdo\Webhookable\Channels\WebHookChannel;
use KUHdo\Webhookable\Notifications\Messages\WebHookMessage;
use KUHdo\Webhookable\Repositories\WebHookRepository;
use KUHdo\Webhookable\WebHook;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class WebHookNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var int
     */

    /**
     * @return static
     */
    public function retryUntil()
    {
        return now()->addHours(5);
    }

    /**
     * @var WebHook $webHook
     */
    protected $webHook;

    /**
     * @var
     */
    protected $payload;
    /**
     * @var WebHookRepository|mixed
     */
    protected $webHookRepo;

    /**
     * Create a new notification instance.
     *
     * @param WebHook $webHook
     */
    public function __construct(WebHook $webHook)
    {
        $this->webHook = $webHook;
        $this->payload = $webHook->payload;

        $this->webHookRepo = resolve(WebHookRepository::class);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [WebHookChannel::class];
    }

    /**
     * Get the WebHook representation of the notification.
     *
     * @param  mixed $notifiable
     * @return WebHookMessage
     * @throws \ErrorException
     */
    public function toWebHook($notifiable)
    {
        //error
        return (new WebHookMessage)
            ->header('X-IFE-Event', $this->webHook->event)
            ->header('Content-Type', 'application/json')
            ->payload($this->payload)
            ->url($this->webHook->url)
            ->signature($notifiable);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'headers' => ['X-IFE-Event' => $this->webHook->event],
            'url'     => $this->webHook->url,
            'payload' => $this->payload,
        ];
    }
}
