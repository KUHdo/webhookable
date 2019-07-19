<?php

use KUHdo\Webhookable\WebHook;
use Faker\Generator as Faker;

$factory->define('KUHdo\Webhookable\WebHook', function (Faker $faker) {
    return [
        'url' => $faker->url,
        'event' => $faker->randomElement(WebHook::getPossibleEventsAttribute()),
    ];
});
