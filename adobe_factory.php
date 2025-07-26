<?php

// Database seeder
// Please visit https://github.com/fzaninotto/Faker for more options

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Adobe_model::class, function (Faker\Generator $faker) {

    return [
        'app_name' => $faker->word(),
        'sapcode' => $faker->word(),
        'base_version' => $faker->word(),
        'installed_version' => $faker->word(),
        'latest_version' => $faker->word(),
    ];
});
