<?php

declare(strict_types=1);

namespace App\Model;

use DcrSwoole\DbConnection\Model;
/**
 * Class UserModel
 * @see https://github.com/illuminate/database
 * @property int $id
 * @property string $created_at
 */
class UserModel extends Model
{
    protected $table = 'user';
}
