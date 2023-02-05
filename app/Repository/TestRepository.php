<?php

declare(strict_types=1);

namespace App\Repository;

//不能用laravel的门面 Illuminate\Support\Facades\DB;

use App\Model\UserModel;
use guanhui07\SwooleDatabase\Adapter\DB;

//use guanhui07\SwooleDatabase\Adapter\Manager as DB ;

class TestRepository extends AbstractRepository
{
    protected function getModelName(): string
    {
        return UserModel::class;
    }

    public function fromRepos(): void
    {
        echo PHP_EOL;
        echo 'test Di2';
    }

    public function test1(): array
    {
//        $test = DB::table('user')->where('id', '>', 1)
//            ->orderBy('id', 'desc')->limit(2)->get(['id']);
//        print_r($test->toArray());
//        $test = DB::select('select 1');
//        var_dump($test);
        $users = UserModel::query()->where('id', '>', 1)->orderBy('id', 'desc')->limit(2)->get(['id']);
        print_r($users->toArray());
        return $users->toArray();
    }

    public static function test2(): bool
    {
        return true;
    }
}
