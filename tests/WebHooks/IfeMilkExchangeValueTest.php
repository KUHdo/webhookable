<?php

namespace Tests\Feature;

use App\IfeMilkExchangeValue;
use App\Notifications\WebHookNotification;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IfeMilkExchangeValueTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /**
     *
     */
    public function testCreateEventIfeMilkExchangeValue()
    {
        $number = $this->faker->numberBetween(1,19);
        Event::fake();

        $commodity = factory('App\Commodity')->create();
        factory('App\IfeMilkExchangeValue', $number)->create(["commodity_id" => $commodity->id]);

        Event::assertDispatched('eloquent.created: App\IfeMilkExchangeValue', $number);
    }
    /**
     *
     */
    public function testUpdateEventIfeMilkExchangeValue()
    {
        $number = $this->faker->numberBetween(1,19);
        Event::fake();

        $commodity = factory('App\Commodity')->create();
        $IfeMilkExchangeValues = factory('App\IfeMilkExchangeValue', $number)->create(["commodity_id" => $commodity->id]);
        $IfeMilkExchangeValues->each(function($month) {
            $month->update(factory('App\IfeMilkExchangeValue')->make()->toArray());
        });

        Event::assertDispatched('eloquent.updated: App\IfeMilkExchangeValue', $number);
    }

    /**
     *
     */
    public function testDeleteEventIfeMilkExchangeValue()
    {
        $number = $this->faker->numberBetween(1,19);
        Event::fake();

        $commodity = factory('App\Commodity')->create();
        $IfeMilkExchangeValues = factory('App\IfeMilkExchangeValue', $number)->create(["commodity_id" => $commodity->id]);
        $IfeMilkExchangeValues->each(function($month){
            $month->delete();
        });

        Event::assertDispatched('eloquent.deleted: App\IfeMilkExchangeValue', $number);
    }

    /**
     *
     */
    public function testIfeMilkExchangeValueCreatedWebHook() {
        $user = factory('App\User')->create();
        $webHook = factory('App\WebHook')->make(['event' => 'IfeMilkExchangeValue.updated']);
        $user->webhooks()->save($webHook);
        $commodity = factory('App\Commodity')->create();
        Notification::fake();
        $tradingMonths = factory('App\IfeMilkExchangeValue')->create(["commodity_id" => $commodity->id]);
        $newMonth = factory('App\IfeMilkExchangeValue')->make()->toArray();
        // Settlement change triggers eod saving routine
        unset($newMonth['settlement_price']);
        $tradingMonths->update($newMonth);
        Notification::assertSentTo(
            [$user], WebHookNotification::class
        );

    }

    /**
     *
     */
    public function testIfeMilkExchangeValueUpdatedWebHook() {
        $user = factory('App\User')->create();
        $webHook = factory('App\WebHook')->make(['event' => 'IfeMilkExchangeValue.updated']);
        $user->webhooks()->save($webHook);
        $commodity = factory('App\Commodity')->create();
        Notification::fake();
        $tradingMonths = factory('App\IfeMilkExchangeValue')->create(["commodity_id" => $commodity->id]);
        $newMonth = factory('App\IfeMilkExchangeValue')->make()->toArray();
        // Settlement change triggers eod saving routine
        unset($newMonth['settlement_price']);
        $tradingMonths->update($newMonth);
        Notification::assertSentTo(
            [$user], WebHookNotification::class
        );
    }

    /**
     *
     */
    public function testIfeMilkExchangeValueDeletedWebHook() {
        $user = factory('App\User')->create();
        $webHook = factory('App\WebHook')->make(['event' => 'IfeMilkExchangeValue.deleted']);
        $user->webhooks()->save($webHook);
        $commodity = factory('App\Commodity')->create();
        Notification::fake();
        $tradingMonths = factory('App\IfeMilkExchangeValue')->create(["commodity_id" => $commodity->id]);
        $tradingMonths->delete();
        Notification::assertSentTo(
            [$user], WebHookNotification::class
        );
    }
    /**
     *
     */
    public function testTradingMonthCreatesIfeMilkExchangeValue() {
        $butterCommodity = factory('App\Commodity')->create(['commodity' => "ButterFutureMarketResults"]);
        $smpCommodity = factory('App\Commodity')->create(['commodity' => "SkimmedMilkPowderFutureMarketResults"]);
        factory('App\Commodity')->create(['commodity' => "ifeMilkExchangeValues"]);

        Event::fake(['eloquent.created: App\IfeMilkExchangeValue']);
        $butter = factory('App\TradingMonth')->create(['commodity_id' => $butterCommodity]);
        $smp = factory('App\TradingMonth')->make(['delivery_period' => $butter->delivery_period,
            'delivery_start' => $butter->delivery_start,
            'delivery_end' => $butter->delivery_end,
            'commodity_id' => $smpCommodity->id]);
        $smp->save();
        //Event::assertDispatched('eloquent.created: App\TradingMonth', 2);
        Event::assertDispatched('eloquent.created: App\IfeMilkExchangeValue', 1);
    }
    /**
     *
     */
    public function testTradingMonthTouchesIfeMilkExchangeValue() {
        $butterCommodity = factory('App\Commodity')->create(['commodity' => "ButterFutureMarketResults"]);
        $smpCommodity = factory('App\Commodity')->create(['commodity' => "SkimmedMilkPowderFutureMarketResults"]);

        $butter = factory('App\TradingMonth')->create(['commodity_id' => $butterCommodity]);
        $smp = factory('App\TradingMonth')->make(['delivery_period' => $butter->delivery_period,
            'delivery_start' => $butter->delivery_start,
            'delivery_end' => $butter->delivery_end,
            'commodity_id' => $smpCommodity->id]);
        $smp->save();
        Event::fake(['eloquent.updated: App\IfeMilkExchangeValue', 'eloquent.created: App\IfeMilkExchangeValue']);
        factory('App\Commodity')->create(['commodity' => "ifeMilkExchangeValues"]);
        IfeMilkExchangeValue::createWithTradingMonths($butter, $smp);
        $butter->update(['best_bid_price' => $butter->best_bid +1]);
        //Event::assertDispatched('eloquent.updated: App\TradingMonth', 1);
        Event::assertDispatched('eloquent.created: App\IfeMilkExchangeValue', 1);
        Event::assertDispatched('eloquent.updated: App\IfeMilkExchangeValue', 1);
    }

    /**
     *
     */
    public function testTradingMonthDeletesIfeMilkExchangeValue() {
        $butterCommodity = factory('App\Commodity')->create(['commodity' => "ButterFutureMarketResults"]);
        $smpCommodity = factory('App\Commodity')->create(['commodity' => "SkimmedMilkPowderFutureMarketResults"]);

        $butter = factory('App\TradingMonth')->create(['commodity_id' => $butterCommodity]);
        $smp = factory('App\TradingMonth')->make(['delivery_period' => $butter->delivery_period,
            'delivery_start' => $butter->delivery_start,
            'delivery_end' => $butter->delivery_end,
            'commodity_id' => $smpCommodity->id]);
        $smp->save();
        Event::fake(['eloquent.created: App\IfeMilkExchangeValue', 'eloquent.updated: App\IfeMilkExchangeValue']);
        factory('App\Commodity')->create(['commodity' => "ifeMilkExchangeValues"]);
        $ifeMilkExchangeValue = IfeMilkExchangeValue::createWithTradingMonths($butter, $smp);
        $butter->update(['best_bid_price' => $butter->best_bid +1]);
        Event::assertDispatched('eloquent.created: App\IfeMilkExchangeValue', 1);
        Event::assertDispatched('eloquent.updated: App\IfeMilkExchangeValue', 1);
    }
}
