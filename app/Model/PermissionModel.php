<?php

declare(strict_types=1);

namespace App\Model;

use DcrSwoole\DbConnection\Model;
/**
 * @see https://github.com/illuminate/database
 * @property int $id
 * @property string $created_at
 */
class PermissionModel extends Model
{
    protected $table = 'casbin_rule';
}
