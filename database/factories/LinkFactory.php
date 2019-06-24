<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Link::class, function (Faker $faker) {
    $updated_at = $faker->dateTimeThisMonth();
    $created_at = $faker->dateTimeThisMonth($updated_at);
    return [
        'title' => $faker->sentence,
        'link' => $faker->url,
        'created_at' => $created_at,
        'updated_at' => $updated_at,
    ];
});
