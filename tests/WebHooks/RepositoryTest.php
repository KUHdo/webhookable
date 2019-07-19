<?php

namespace Tests\Feature\WebHooks;

use KUHdo\Webhookable\Repositories\WebHookRepository;
use KUHdo\Webhookable\WebHook;
use Illuminate\Support\Str;
use Orchestra\Testbench\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery as m;

/**
 * Class RepositoryTest
 * @package Tests\Feature\WebHooks
 */
class RepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // and other test setup steps you need to perform
        $this->withFactories(base_path() .'/database/factories');


    }


    public function tearDown() : void
    {
        m::close();
    }

    /**
     *
     *
     */
    public function testMatchingEvent()
    {

        $webHooks = factory('KUHdo\Webhookable\WebHook', 25)->make();
        dd($webHooks);
        $user = factory('App\User')->create();

        $webHooks->transform(function(WebHook $hook) use ($user) {
            $hasAsterisk = rand(0,3) == 0;
            $hook->event = $hasAsterisk ? explode('.', $hook->event)[0]. '.*': $hook->event;
            $user->webHooks()->save($hook);
            return $hook;
        });

        $randomHook = $webHooks->random();
        $webhookRepo = m::mock(WebHookRepository::class);
        $matchingHooksRepo = $webhookRepo->shouldReceive('matchingEvents')->with($randomHook->event)->andReturn();

        if(Str::contains($randomHook->event, '*')) {
            $matchingHooksCol = $webHooks->filter(function(WebHook $hook) use ($randomHook) {
                return Str::startsWith($hook->event, explode('.', $randomHook->event)[0] . '.');
            });
        } else {
            $matchingHooksCol = $webHooks->filter(function(Webhook $hook) use ($randomHook) {
                return $hook->event == $randomHook->event;
            });
        }

        $this->assertTrue($matchingHooksCol->count() == $matchingHooksRepo->count());
        $matchingHooksCol->each(function(Webhook $hook) use($matchingHooksRepo) {
            $this->assertContains($hook->toArray(), $matchingHooksRepo->toArray());
        });
    }
}
