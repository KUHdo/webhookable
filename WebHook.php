<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\WebHook
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $url
 * @property string $event
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\WebHook newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\WebHook newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\WebHook query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\WebHook whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\WebHook whereEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\WebHook whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\WebHook whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\WebHook whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\WebHook whereUserId($value)
 * @mixin \Eloquent
 * @property string $webhookable_type
 * @property int $webhookable_id
 * @property-read mixed $method
 * @property-read null $payload
 * @property-read array $possible_events
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $webhookable
 * @method static \Illuminate\Database\Eloquent\Builder|\App\WebHook whereWebhookableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\WebHook whereWebhookableType($value)
 */
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
    public function getMethodAttribute() {
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
     * @param null $payload
     */
    public function setPayloadAttribute($payload): void
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
