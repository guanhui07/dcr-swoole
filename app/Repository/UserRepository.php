<?php

declare(strict_types=1);

namespace App\Repository;

//不能用laravel的门面 Illuminate\Support\Facades\DB;
use App\Model\UserModel;
use guanhui07\SwooleDatabase\Adapter\DB;
//use guanhui07\SwooleDatabase\Adapter\Manager as DB ;

class UserRepository extends AbstractRepository
{
    protected function getModelName(): string
    {
        return UserModel::class;
    }

    public function test($arr): array
    {
        return [1];
    }
}
