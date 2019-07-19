<?php

namespace KUHdo\Webhookable;

use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Database\Eloquent\Model;

class WebHook extends Model
{
    /**
     * @var string
     */
    protected $table = 'web_hooks';

    /**
     * @var array
     */
    public $hidden = ['webhookable_type', 'webhookable_id', 'possibleEvents', 'method', 'payload'];

    /**
     * @var array
     */
    public $fillable = ['id', 'url', 'event'];

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
     * @var array
     */
    public $appends = [
        'possibleEvents',
        'method',
    ];

    /**
     * array of possible events to subscribe to
     * @return array
     */
    public static function getPossibleEventsAttribute() {
        return [
                'tradingMonth.*',
                'tradingMonth.created',
                'tradingMonth.deleted',
                'tradingMonth.updated',
                'ifeMilkExchangeValue.*',
                'ifeMilkExchangeValue.created',
                'ifeMilkExchangeValue.deleted',
                'ifeMilkExchangeValue.updated',
                'tradingMonthEod.*',
                'tradingMonthEod.created',
                'tradingMonthEod.deleted',
                'tradingMonthEod.updated',
                'commodity.*',
                'commodity.touched',
               ];
    }

    /**
     * gets the request method of an event
     * @return mixed
     */
    public function getMethodAttribute() : string {
        $event = explode('.', $this->event)[1] ?? 'POST';
        return $this->methods[$event] ?? $event;
    }

    /**
     * @return null
     */
    public function getPayloadAttribute()
    {
        return $this->payload;
    }

    /**
     * sets payload attribute
     *
     * @param \Illuminate\Contracts\Support\Jsonable $payload
     */
    public function setPayloadAttribute(Jsonable $payload): void
    {
        $this->payload = $payload;
    }

    /**
     * Get all of the owning webhookable models.
     */
    public function webhookable()
    {
        return $this->morphTo();
    }
}
