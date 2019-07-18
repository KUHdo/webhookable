<?php

namespace Tests\Feature;

use App\Events\SaveTradingMonthsEod;
use App\Notifications\WebHookNotification;
use App\WebHook;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Faker\Factory as Faker;

class TradingMonthsTest extends TestCase
{
    use WithFaker, RefreshDatabase;
    /**
     *
     */
    public function testCreateEventTradingMonth()
    {
        $faker = Faker::create();
        $number = $faker->numberBetween(1,19);
        Event::fake();

        $commodity = factory('App\Commodity')->create();
        factory('App\TradingMonth', $number)->create(["commodity_id" => $commodity->id]);

        Event::assertDispatched('eloquent.created: App\TradingMonth', $number);
    }
    /**
     *
     */
    public function testUpdateEventTradingMonth()
    {
        $faker = Faker::create();
        $number = $faker->numberBetween(1,19);
        Event::fake();

        $commodity = factory('App\Commodity')->create();
        $tradingMonths = factory('App\TradingMonth', $number)->create(["commodity_id" => $commodity->id]);
        $tradingMonths->each(function($month) {
           $month->update(factory('App\TradingMonth')->make()->toArray());
        });

        Event::assertDispatched('eloquent.updated: App\TradingMonth', $number);
    }

    /**
     *
     */
    public function testDeleteEventTradingMonth()
    {
        $faker = Faker::create();
        $number = $faker->numberBetween(1,19);
        Event::fake();

        $commodity = factory('App\Commodity')->create();
        $tradingMonths = factory('App\TradingMonth', $number)->create(["commodity_id" => $commodity->id]);
        $tradingMonths->each(function($month){
            $month->delete();
        });

        Event::assertDispatched('eloquent.deleted: App\TradingMonth', $number);
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function testTradingMonthCreatedWebHook() {
        $clients = app()->make(ClientRepository::class);
        $user = factory('App\User')->create();
        $clients->createPersonalAccessClient(
            $user->id, $user->name, 'https://example.org'
        );
        $user->createToken('Token Name')->accessToken;

        $webHook = factory('App\WebHook')->make(['event' => 'tradingMonth.created']);
        $user->webhooks()->save($webHook);
        $commodity = factory('App\Commodity')->create();
        Notification::fake();
        $tradingMonths = factory('App\TradingMonth')->create(["commodity_id" => $commodity->id]);
        Notification::assertSentTo(
            [$user], WebHookNotification::class
        );

    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function testTradingMonthUpdatedWebHook() {
        $clients = app()->make(ClientRepository::class);
        $user = factory('App\User')->create();
        $clients->createPersonalAccessClient(
            $user->id, $user->name, 'https://example.org'
        );
        $user->createToken('Token Name')->accessToken;

        $webHook = factory('App\WebHook')->make(['event' => 'tradingMonth.updated']);
        $user->webhooks()->save($webHook);
        $commodity = factory('App\Commodity')->create();
        $tradingMonths = factory('App\TradingMonth')->create(["commodity_id" => $commodity->id]);
        $newMonth = factory('App\TradingMonth')->make()->toArray();
        // Settlement change triggers eod saving routine
        unset($newMonth['settlement_price']);
        Notification::fake();
        $tradingMonths->update($newMonth);
        Notification::assertSentTo(
            [$user], WebHookNotification::class
        );

    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function testTradingMonthDeletedWebHook() {
        $clients = app()->make(ClientRepository::class);
        $user = factory('App\User')->create();
        $clients->createPersonalAccessClient(
            $user->id, $user->name, 'https://example.org'
        );
        $webHook = factory('App\WebHook')->make(['event' => 'tradingMonth.deleted']);
        $user->webhooks()->save($webHook);
        $commodity = factory('App\Commodity')->create();
        Notification::fake();
        $tradingMonths = factory('App\TradingMonth')->create(["commodity_id" => $commodity->id]);
        $tradingMonths->delete();
        Notification::assertSentTo(
            [$user], WebHookNotification::class
        );
    }

    /**
     *
     */
    public function testTradingMonthSettlementChanged() {

        $commodity = factory('App\Commodity')->create();
        $tradingMonth = factory('App\TradingMonth')->create(["commodity_id" => $commodity->id]);
        // only listen to SaveTradingMonthsEod because eloquend.updated App\TradingMonth triggers
        // SaveTradingMonthsEod
        Event::fake([SaveTradingMonthsEod::class]);
        $tradingMonth->update(['settlement_price' => $tradingMonth->settlement_price + 1]);
        Event::assertDispatched(SaveTradingMonthsEod::class, 1);
    }
}
