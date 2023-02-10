<?php

/**
 * @desc Real Policy Model
 */

declare(strict_types=1);

namespace DcrSwoole\Permission\Model;

use DcrSwoole\DbConnection\Model;

/**
 * RuleModel Model
 */
class RuleModel extends Model
{
    /**
     * 设置字段信息
     *
     * @var array
     */
    protected array $schema = [
        'id'    => 'int',
        'ptype' => 'string',
        'v0'    => 'string',
        'v1'    => 'string',
        'v2'    => 'string',
        'v3'    => 'string',
        'v4'    => 'string',
        'v5'    => 'string'
    ];

    /**
     * 架构函数
     * @access public
     * @param array $data 数据
     */
    public function __construct(array $data = [])
    {
        $this->connection = $this->config('database.connection') ?: '';
        $this->table = $this->config('database.rules_table');
        $this->name = $this->config('database.rules_name');
        parent::__construct($data);
    }

    /**
     * Gets config value by key.
     *
     * @param string|null $key
     * @param null $default
     *
     * @return mixed
     */
    protected function config(string $key = null, $default = null)
    {
        $driver = config('plugin.casbin.permission.permission.default');
        return config('plugin.casbin.permission.permission.' . $driver . '.' . $key, $default);
    }
}
