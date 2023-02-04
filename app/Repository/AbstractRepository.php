<?php

namespace App\Repository;

abstract class AbstractRepository
{
    abstract protected function getModelName(): string;

    public function getQuery()
    {
        return $this->getModelName()::query();
    }

    public function getFieldValueMap(string $field, array $where, string $value)
    {
        return $this->getQuery()->whereIn($field, $where)->pluck($value, $field)->toArray();
    }

    public function find(int $id)
    {
        if (!$id) {
            return null;
        }
        return $this->getQuery()->find($id);
    }

    public function fetchByIds(array $ids)
    {
        return $this->getQuery()->whereIn('id', $ids)->get();
    }

    public function getRow(array $where)
    {
        return $this->getQuery()->where($where)->first();
    }

    public function getNewRow()
    {
        $modelName = $this->getModelName();
        return new $modelName();
    }

    public function page($where, int $offset = 0, int $limit = 20, array $order = ['id', 'desc'])
    {
        $model = $this->getQuery();

        if (!empty($where)) {
            $model = $model->where($where);
        }
        if (!empty($order)) {
            $model = $model->orderBy(...$order);
        }
        if ($offset && $limit) {
            $model = $model->offset($offset)->limit($limit);
        }

        return $model->where($where)->get();
    }


    public function create(array $data)
    {
        $result = $this->getQuery()->create($data);
        if (!$result) {
            throw new \RuntimeException(static::class . '创建失败');
        }
        return $result;
    }

    public function originQuery()
    {
        return $this->getModelName()::originQuery();
    }
}
