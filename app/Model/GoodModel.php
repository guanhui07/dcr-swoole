<?php

declare(strict_types=1);

namespace App\Model;

//use Illuminate\Database\Eloquent\Model;

/**
 * @see https://github.com/illuminate/database
 * @property int $id
 * @property string $created_at
 */
class GoodModel extends BaseModel
{
    protected $table = 'good';
}
