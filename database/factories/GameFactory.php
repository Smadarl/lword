<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Game::class, function (Faker $faker) {
    $results = App\User::select('id')->orderByRaw("RAND()");
    $startedBy = $results->first()->id;
//    $opponent = $results->last()->id;
    return [
        'max_length' => $faker->numberBetween(6,16),
        'max_recurrance' => $faker->numberBetween(1, 3),
        'started_by' => $startedBy,
//        'turn' => $faker->randomElement([$startedBy, $opponent]),
        'turn' => $startedBy,
        'status' => $faker->randomElement(['pending', 'started']),
    ];
});
