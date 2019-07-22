<?php

namespace KUHdo\Webhookable;

use Illuminate\Database\Eloquent\Model;

class WebHook extends Model
{
    /**
     * @var string table name
     */
    protected $table = 'web_hooks';

    /**
     * @var array
     */
    public $hidden = [
        'webhookable_type',
        'webhookable_id',
        'possibleEvents',
        'method',
        'payload'
    ];

    /**
     * @var array
     */
    public $fillable = [
        'id',
        'url',
        'event'
    ];

    /**
     * @var array
     */
    private $methods = [
        'created' => "POST",
        'updated' => "PUT",
        'deleted' => "DELETE"
    ];

    /**
     * @var $payload
     */
    protected $payload;

    /**
     * @var array $appends
     */
    public $appends = [
        'possibleEvents',
        'method',
    ];

    /**
     * array of possible events to subscribe to
     *
     * @return array
     */
    public static function getPossibleEventsAttribute()
    {
        return config('webhookable.possible_events');
    }

    /**
     * gets the request method of an event
     * @return mixed
     */
    public function getMethodAttribute() : string
    {
        $event = explode('.', $this->event)[1] ?? 'POST';
        return $this->methods[$event] ?? $event;
    }

    /**
     * Get actual payload (not persisted)
     *
     * @return string
     */
    public function getPayloadAttribute()
    {
        return $this->payload;
    }

    /**
     * sets payload attribute
     *
     * @param $payload
     */
    public function setPayloadAttribute($payload): void
    {
        $this->payload = $payload;
    }

    /**
     * Get all models owning webhooks.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function webhookable()
    {
        return $this->morphTo();
    }
}
