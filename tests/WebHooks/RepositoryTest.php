<?php

namespace Tests\Feature\WebHooks;

use KUHdo\Webhookable\Repositories\WebHookRepository;
use KUHdo\Webhookable\WebHook;
use Illuminate\Support\Str;
use Orchestra\Testbench\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Class RepositoryTest
 * @package Tests\Feature\WebHooks
 */
class RepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var WebHookRepository $webHookRepo
     */
    private $webHookRepo;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->webHookRepo = resolve(WebHookRepository::class);
    }

    /**
     *
     *
     */
    public function testMatchingEvent()
    {
        $webHooks = factory('App\WebHook', 25)->make();
        $user = factory('App\User')->create();

        $webHooks->transform(function(WebHook $hook) use ($user) {
            $hasAsterisk = rand(0,3) == 0;
            $hook->event = $hasAsterisk ? explode('.', $hook->event)[0]. '.*': $hook->event;
            $user->webHooks()->save($hook);
            return $hook;
        });

        $randomHook = $webHooks->random();
        $matchingHooksRepo = $this->webHookRepo->matchingEvents($randomHook->event);

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
