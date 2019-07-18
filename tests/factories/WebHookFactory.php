<?php

use App\WebHook;
use Faker\Generator as Faker;

$factory->define(App\WebHook::class, function (Faker $faker) {
    return [
        'url' => $faker->url,
        'event' => $faker->randomElement(WebHook::getPossibleEventsAttribute()),
    ];
});
