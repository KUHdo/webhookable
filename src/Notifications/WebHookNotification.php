<?php

namespace App\Notifications;

use App\Channels\WebHookChannel;
use App\Notifications\Messages\DeprecatedRestHookMessage;
use App\Notifications\Messages\WebHookMessage;
use App\Repositories\WebHook\WebHookRepository;
use App\WebHook;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class WebHookNotification extends Notification implements ShouldQueue
{
    use Queueable;

    //public $tries = 10;

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
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function __construct(WebHook $webHook)
    {
        $this->webHook = $webHook;
        $this->payload = $webHook->payload;

        $this->webHookRepo = app()->make(WebHookRepository::class);
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