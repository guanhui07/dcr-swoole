<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories as EloquentFactory;
function factory()
{
    $factory = di(EloquentFactory::class);

    $arguments = func_get_args();

    if (isset($arguments[1]) && is_string($arguments[1])) {
        return $factory->of($arguments[0], $arguments[1])->times($arguments[2] ?? null);
    } elseif (isset($arguments[1])) {
        return $factory->of($arguments[0])->times($arguments[1]);
    }

    return $factory->of($arguments[0]);
}

class TradeGoodsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Model\UserModel::class, 10)->create();
    }
}
