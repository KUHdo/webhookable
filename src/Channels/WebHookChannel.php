<?php
/**
 * Created by PhpStorm.
 * User: arnebartelt
 * Date: 14.03.19
 * Time: 13:52
 */

namespace App\Channels;
use App\Notifications\WebHookNotification;
use Illuminate\Notifications\Notification;
use App\Exceptions\WebHookFailedException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Log;

class WebHookChannel
{

    /**
     * @var \GuzzleHttp\Client
     */
    private $client;

    /**
     * @var
     */
    private $notifiable;


    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Sends the webHook to the notifiable
     *
     * @param array $webHookMessage
     * @throws WebHookFailedException
     */
    private function sendWebHook(Array $webHookMessage) {
        // Append event to body
        $request = new Request(
            //$webHookMessage['webHook']->method,
            'POST',
            $webHookMessage['url'],
            $webHookMessage['headers'],
            json_encode($webHookMessage['payload'])
        );

        try {
            $response = $this->client->send($request);
            if ($response->getStatusCode() !== 200) {
                throw new WebHookFailedException('WebHook received a non 200 response');

            }
            Log::debug('WebHook successfully posted to '. $webHookMessage['url']);
            return;
        } catch (ClientException $exception) {
            if ($exception->getResponse()->getStatusCode() !== 410) {
                throw new WebHookFailedException($exception->getMessage(), $exception->getCode(), $exception);
            }
        } catch (GuzzleException $exception) {
            throw new WebHookFailedException($exception->getMessage(), $exception->getCode(), $exception);
        }
        Log::error('WebHook failed in posting to '. $webHookMessage['url']);
    }

    /**
     * Send the given notification.
     *
     * @param  mixed $notifiable
     * @param  WebHookNotification $notification
     * @return void
     * @throws WebHookFailedException
     * @throws \ErrorException
     */
    public function send($notifiable, WebHookNotification $notification)
    {
        if (method_exists($notification, 'toWebHook')) {
            $webHookMessage = $notification->toWebHook($notifiable)->toArray();
        } else {
            $webHookMessage = $notification->toArray($notifiable);
        }
        $this->notifiable = $notifiable;

        $this->sendWebHook($webHookMessage);

    }
}