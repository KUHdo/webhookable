<?php

namespace Tests\Feature;

use KUHdo\Webhookable\Notifications\WebHookNotification;
use Illuminate\Support\Facades\Notification;
use Laravel\Passport\ClientRepository;
use Orchestra\Testbench\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;


/**
 * Class TradingMonthsEodTest
 * @package Tests\Feature
 */
class TradingMonthsEodTest extends TestCase
{

    use RefreshDatabase, WithFaker;


    /**
     */
    public function testTradingMonthEodCreatedWebHook() {
        $clients = resolve(ClientRepository::class);
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
        $clients = resolve(ClientRepository::class);
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
