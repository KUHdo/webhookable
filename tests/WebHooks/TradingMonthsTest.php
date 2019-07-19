<?php

namespace Tests\Feature;

use KUHdo\Webhookable\Notifications\WebHookNotification;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Orchestra\Testbench\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;

class TradingMonthsTest extends TestCase
{
    use WithFaker, RefreshDatabase;
    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    /**
     * Setup the test environment.
     */
    protected function setUp() : void
    {
        parent::setUp();

        $this->loadLaravelMigrations(['--database' => 'testbench']);
        $this->loadMigrationsFrom([
            '--database' => 'testing',
            '--path' => realpath(__DIR__.'/../database/migrations'),
        ]);
        // and other test setup steps you need to perform
        $this->withFactories(__DIR__.'/../database/factories');
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
