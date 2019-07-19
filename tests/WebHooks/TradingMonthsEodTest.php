<?php

namespace Tests\Feature;

use App\Events\SaveTradingMonthsEod;
use App\Notifications\WebHookNotification;
use App\TradingMonthEod;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Laravel\Passport\ClientRepository;
use Orchestra\Testbench\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Faker\Factory as Faker;


/**
 * Class TradingMonthsEodTest
 * @package Tests\Feature
 */
class TradingMonthsEodTest extends TestCase
{

    use RefreshDatabase, WithFaker;

    /**
     *
     */
    public function testCreateEventTradingMonthEod()
    {
        $number = $this->faker->numberBetween(1,19);

        $commodity = factory('App\Commodity')->create();
        Event::fake(['eloquent.created: App\TradingMonthEod']);
        $eod = factory('App\TradingMonthEod', $number)->create(["commodity_id" => $commodity->id]);
        Event::assertDispatched('eloquent.created: App\TradingMonthEod', $number);

    }
    /**
     *
     */
    public function testUpdateEventTradingMonthEod()
    {
        $number = $this->faker->numberBetween(1,19);

        $commodity = factory('App\Commodity')->create();
        $eod = factory('App\TradingMonthEod', $number)->create(["commodity_id" => $commodity->id]);
        Event::fake();
        $eod->each(function($eod) {
            $eod->update(['settlement_price' => $eod->settlement_price +1]);
        });
        Event::assertDispatched('eloquent.updated: App\TradingMonthEod', $number);
    }

    /**
     *
     */
    public function testDeleteEventTradingMonthEod()
    {
        $number = $this->faker->numberBetween(1,19);

        $commodity = factory('App\Commodity')->create();
        Event::fakeFor(function () use ($commodity, $number) {
            $tradingMonths = factory('App\TradingMonth', $number)->create(["commodity_id" => $commodity->id]);
            Event::assertDispatched('eloquent.created: App\TradingMonth', $number);
            return $tradingMonths;
        });
        event(new SaveTradingMonthsEod());

        Event::fake();
        TradingMonthEod::all()->each(function(TradingMonthEod $eod) {
            $eod->delete();
        });
        sleep(5);
        Event::assertDispatched('eloquent.deleted: App\TradingMonthEod', $number);
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function testTradingMonthEodCreatedWebHook() {
        $clients = app()->make(ClientRepository::class);
        $user = factory('App\User')->create();
        $clients->createPersonalAccessClient(
            $user->id, $user->name, 'https://example.org'
        );
        $user->createToken('Token Name')->accessToken;

        $webHook = factory('App\WebHook')->make(['event' => 'tradingMonthEod.created']);
        $user->webhooks()->save($webHook);
        $commodity = factory('App\Commodity')->create();
        Notification::fake();
        factory('App\TradingMonthEod')->create(["commodity_id" => $commodity->id]);
        Notification::assertSentTo(
            [$user], WebHookNotification::class
        );
    }

    /**
     *
     */
    public function testTradingMonthEodUpdatedWebHook() {
        $clients = app()->make(ClientRepository::class);
        $user = factory('App\User')->create();
        $clients->createPersonalAccessClient(
            $user->id, $user->name, 'https://example.org'
        );
        $user->createToken('Token Name')->accessToken;

        $webHook = factory('App\WebHook')->make(['event' => 'tradingMonthEod.updated']);
        $user->webhooks()->save($webHook);
        $commodity = factory('App\Commodity')->create();
        $eod = factory('App\TradingMonthEod')->create(["commodity_id" => $commodity->id]);
        Notification::fake();
        $eod->update(['settlement_price' => $eod->settlement_price + 1]);
        Notification::assertSentTo(
            [$user], WebHookNotification::class
        );
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function testTradingMonthEodDeletedWebHook() {
        $clients = app()->make(ClientRepository::class);
        $user = factory('App\User')->create();
        $clients->createPersonalAccessClient(
            $user->id, $user->name, 'https://example.org'
        );
        $user->createToken('Token Name')->accessToken;

        $webHook = factory('App\WebHook')->make(['event' => 'tradingMonthEod.deleted']);
        $user->webhooks()->save($webHook);
        $commodity = factory('App\Commodity')->create();
        $eod = factory('App\TradingMonthEod')->create(["commodity_id" => $commodity->id]);
        Notification::fake();
        $eod->delete();
        Notification::assertSentTo(
            [$user], WebHookNotification::class
        );
    }
}
