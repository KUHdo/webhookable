<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Faker\Factory as Faker;


class CommodityChangedTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testCreateTradingMonth()
    {
        $faker = Faker::create();
        $number = $faker->numberBetween(1,19);

        $commodity = factory('App\Commodity')->create();
        Event::fake();
        $tradingMonths = factory('App\TradingMonth', $number)->make();
        $commodity->tradingMonths()->saveMany($tradingMonths);

        Event::assertDispatched('eloquent.created: App\TradingMonth', $number);
        Event::assertDispatched('eloquent.saved: App\Commodity', $number);

    }
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testCreateTradingMonthEod()
    {
        $faker = Faker::create();
        $number = $faker->numberBetween(1,19);

        $commodity = factory('App\Commodity')->create();
        Event::fake();
        $tradingMonths = factory('App\TradingMonthEod', $number)->make();
        $commodity->tradingMonths()->saveMany($tradingMonths);

        Event::assertDispatched('eloquent.created: App\TradingMonthEod', $number);
        Event::assertDispatched('eloquent.saved: App\Commodity', $number);

    }
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testUpdateTradingMonth()
    {
        $commodity = factory('App\Commodity')->create();
        $tradingMonth = factory('App\TradingMonth')->create(['commodity_id' => $commodity->id]);
        Event::fake();
        $tradingMonth->update(['last_price' => $tradingMonth->last_price +1 ]);
        Event::assertDispatched('eloquent.updated: App\TradingMonth', 1);
        Event::assertDispatched('eloquent.saved: App\Commodity', 1);

    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testDeleteTradingMonth()
    {

        $commodity = factory('App\Commodity')->create();
        $tradingMonth = factory('App\TradingMonth')->create(['commodity_id' => $commodity->id]);
        Event::fake();
        $tradingMonth->delete();
        Event::assertDispatched('eloquent.deleted: App\TradingMonth', 1);
        Event::assertDispatched('eloquent.saved: App\Commodity', 1);

    }


    public function testCommodityTouched()
    {
        $commodity = factory('App\Commodity')->create();
        Event::fake();
        $commodity->touch();
        Event::assertDispatched('eloquent.updated: App\Commodity', 1);
        Event::assertDispatched('eloquent.saved: App\Commodity', 1);


    }

}
