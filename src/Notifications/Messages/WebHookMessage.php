<?php
/**
 * Created by PhpStorm.
 * User: arnebartelt
 * Date: 16.04.19
 * Time: 13:47
 * Credits: https://github.com/laravel-notification-channels/webhook
 */

namespace KUHdo\Webhookable\Notifications\Messages;


use KUHdo\Webhookable\Contracts\Webhookable;
use Illuminate\Support\Str;

class WebHookMessage
{
    /**
     * @var mixed
     */
    protected $payload;

    /**
     * @var array|null
     */
    protected $headers;

    /**
     * @var string|null
     */
    protected $userAgent;


    /**
     * @var String
     */
    protected $url;

    /**
     * @param mixed $payload
     *
     * @return static
     */
    public static function create($payload = '')
    {
        return new static($payload);
    }

    /**
     * @param mixed $payload
     */
    public function __construct($payload = '')
    {
        $this->payload = $payload;
    }

    /**
     * Add a Webhook request custom header.
     *
     * @param string $name
     * @param string $value
     *
     * @return $this
     */
    public function header($name, $value)
    {
        $this->headers[$name] = $value;

        return $this;
    }

    /**
     * Add a Web hook request url.
     *
     * @param String $url
     * @return $this
     */
    public function url(String $url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Set the Web hook payload to be JSON encoded.
     *
     * @param mixed $payload
     *
     * @return $this
     */
    public function payload($payload)
    {
        $this->payload = $payload;

        return $this;
    }


    /**
     * Set the Web hook request UserAgent.
     *
     * @param string $userAgent
     *
     * @return $this
     */
    public function userAgent($userAgent)
    {
        $this->headers['User-Agent'] = $userAgent;

        return $this;
    }


    /**
     * Set the Web hook signature to the request
     * $notifiable must implement method getSigningKey()!
     *
     * @param $notifiable
     * @return $this
     * @throws \ErrorException
     */
    public function signature(Webhookable $notifiable)
    {
        if(method_exists($notifiable, 'getSigningKey')) {
            $this->headers = (is_array($this->headers)) ?
                array_merge($this->generateSignature($notifiable), $this->headers) :
                $this->generateSignature($notifiable);
            return $this;
        } else {
            throw new \ErrorException('Notifiable must have a getSigningKey method for webHook notification.');
        }
    }

    /**
     * generates the specific signature with secret from
     * notifiable web hook trait
     *
     * @param $notifiable
     * @return array
     */
    private function generateSignature(Webhookable $notifiable) : array {
        $timestamp = now()->timestamp;
        $token = Str::random(16);
        return [
            'X-TIMESTAMP' => $timestamp,
            'X-TOKEN' => $token,
            'X-SIGNATURE' => hash_hmac(
                'sha256',
                $token . $timestamp,
                $notifiable->getSigningKey()
            ),
        ];
    }

    /**
     * merges the message data into an array
     * @return array
     */
    public function toArray()
    {
        return [
            'headers' => $this->headers,
            'url'     => $this->url,
            'payload' => $this->payload,
        ];
    }

}
