<?php

use Faker\Generator as Faker;

$factory->define(App\Model\UserModel::class, function (Faker $faker) {
    return [
        't_law_state' => $faker->name,
        't_application_no' => mt_rand(10000,90000),
        't_trademark_state' => $faker->image($dir = '.\public\uploads\img', $width = 500, $height = 366),
        'str_2048' => $faker->randomFloat($nbMaxDecimals = 2, $min = 0, $max = 1000),
    ];
});
