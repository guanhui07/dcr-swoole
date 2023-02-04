<?php

use Faker\Generator as Faker;

$factory->define(app\Model\UserModel::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'title' => $faker->sentence($nbWords = 6, $variableNbWords = true),
        'desc' => $faker->text,
        'price' => $faker->numberBetween($min = 1000, $max = 2000),
        'area_id' => $faker->numberBetween($min = 1, $max = 8),
        'operate_time' => $faker->numberBetween($min = 1, $max = 4),
        'company_shui' => $faker->numberBetween($min = 1, $max = 2),
        'regis_price' => $faker->numberBetween($min = 1, $max = 3),
        'sell_type' => $faker->numberBetween($min = 1, $max = 2),
        'online_banking' => $faker->numberBetween($min = 1, $max = 2),
        'account_opening' => $faker->numberBetween($min = 1, $max = 2),
        'range' => $faker->sentence($nbWords = 10, $variableNbWords = true),
        'authorize' => $faker->numberBetween($min = 1, $max = 2),
        'license_pic' => $faker->sentence($nbWords = 10, $variableNbWords = true),
        'certificate_pic' => $faker->sentence($nbWords = 10, $variableNbWords = true),
        'paper_pic' => $faker->sentence($nbWords = 10, $variableNbWords = true),
        'qq' => ''.$faker->numberBetween($min = 10000000, $max = 90000000),
        'sell_name' => $faker->name,
        'phone' => $faker->regexify('[1-9][0-9]{10}'),
        'status' => $faker->numberBetween($min = 1, $max = 2),
        'associated_id' => $faker->numberBetween($min = 1, $max = 3),
    ];
});
