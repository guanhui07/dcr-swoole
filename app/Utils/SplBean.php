<?php

namespace App\Utils;

use Exception;
use JsonSerializable;

class SplBean implements JsonSerializable
{
    public const FILTER_NOT_NULL = 1;
    public const FILTER_NOT_EMPTY = 2;
    public const FILTER_NULL = 3;
    public const FILTER_EMPTY = 4;

    public function __construct(array $data = null, $autoCreateProperty = false)
    {
        if ($data) {
            $this->arrayToBean($data, $autoCreateProperty);
        }
        $this->initialize();
        $this->classMap();
    }

    final public function allProperty(): array
    {
        $data = [];
        $class = new \ReflectionClass($this);
        $protectedAndPublic = $class->getProperties(
            \ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED
        );
        foreach ($protectedAndPublic as $item) {
            if ($item->isStatic()) {
                continue;
            }
            $data[] = $item->getName();
        }
        $data = array_flip($data);
        unset($data['_keyMap']);
        unset($data['_classMap']);
        return array_flip($data);
    }

    public function toArray(array $columns = null, $filter = null): array
    {
        $data = $this->jsonSerialize();
        if ($columns) {
            $data = array_intersect_key($data, array_flip($columns));
        }
        if ($filter === self::FILTER_NOT_NULL) {
            return array_filter($data, function ($val) {
                return !is_null($val);
            });
        }

        if ($filter === self::FILTER_NOT_EMPTY) {
            return array_filter($data, function ($val) {
                return !empty($val);
            });
        }

        if ($filter === self::FILTER_NULL) {
            return array_filter($data, function ($val) {
                return is_null($val);
            });
        }

        if ($filter === self::FILTER_EMPTY) {
            return array_filter($data, function ($val) {
                return empty($val);
            });
        }

        if (is_callable($filter)) {
            return array_filter($data, $filter);
        }
        return $data;
    }

    /*
     * 返回转化后的array
     */
    public function toArrayWithMapping(array $columns = null, $filter = null)
    {
        $array = $this->toArray();
        $array = $this->beanKeyMap($array);

        if ($columns) {
            $array = array_intersect_key($array, array_flip($columns));
        }
        if ($filter === self::FILTER_NOT_NULL) {
            return array_filter($array, function ($val) {
                return !is_null($val);
            });
        }

        if ($filter === self::FILTER_NOT_EMPTY) {
            return array_filter($array, function ($val) {
                if ($val === 0 || $val === '0') {
                    return true;
                } else {
                    return !empty($val);
                }
            });
        }

        if (is_callable($filter)) {
            return array_filter($array, $filter);
        }
        return $array;
    }

    private function arrayToBean(array $data, $autoCreateProperty = false): SplBean
    {
        $data = $this->dataKeyMap($data);

        if ($autoCreateProperty == false) {
            $data = array_intersect_key($data, array_flip($this->allProperty()));
        }
        foreach ($data as $key => $item) {
            $this->addProperty($key, $item);
        }
        return $this;
    }

    final public function addProperty($name, $value = null): void
    {
        $this->$name = $value;
    }

    final public function getProperty($name)
    {
        if (isset($this->$name)) {
            return $this->$name;
        }

        return null;
    }

    final public function jsonSerialize(): array
    {
        $data = [];
        foreach ($this as $key => $item) {
            $data[$key] = $item;
        }
        unset($data['_keyMap']);
        unset($data['_classMap']);
        return $data;
    }

    public function __toString()
    {
        return json_encode($this->jsonSerialize(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /*
     * 在子类中重写该方法，可以在类初始化的时候进行一些操作
     */
    protected function initialize(): void
    {
    }

    /*
     * 如果需要用到keyMap  请在子类重构并返回对应的map数据
     * return ['beanKey'=>'dataKey']
     * return ['实际的键名'=>'传人的键名']
     */
    protected function setKeyMapping(): array
    {
        return [];
    }

    /*
     * return ['property'=>class string]
     */
    protected function setClassMapping(): array
    {
        return [];
    }

    /*
     * 恢复到属性定义的默认值
     */
    public function restore(array $data = [], $autoCreateProperty = false)
    {
        $this->clear();
        $this->arrayToBean($data + get_class_vars(static::class), $autoCreateProperty);
        $this->initialize();
        $this->classMap();
        return $this;
    }

    public function merge(array $data)
    {
        $this->arrayToBean($data);
        return $this;
    }

    private function clear()
    {
        $keys = $this->allProperty();
        $ref = new \ReflectionClass(static::class);
        $fields = array_keys($ref->getDefaultProperties());
        $fields = array_merge($fields, array_values($this->setKeyMapping()));
        // 多余的key
        $extra = array_diff($keys, $fields);

        foreach ($extra as $key => $value) {
            unset($this->$value);
        }
    }

    private function classMap()
    {
        $propertyList = $this->allProperty();
        foreach ($this->setClassMapping() as $property => $class) {
            if (in_array($property, $propertyList)) {
                $val = $this->$property;
                $force = 'create';
                if (strpos($class, '@') !== false) {
                    $force = 'default';
                    $class = substr($class, 1);
                } elseif (strpos($class, '[]') !== false) {
                    $force = 'array';
                    $class = str_replace('[]', '', $class);
                }
                if (is_object($val)) {
                    if (!$val instanceof $class) {
                        throw new Exception("forece:{$force} value for property:{$property} dot not match in " . ($class));
                    }

                    $val = (array)$val;
                } elseif ($force == 'create' && $class == static::class) {
                    throw new Exception("forece:{$force} value for property:{$property} dot not match in " . (static::class));
                }

                if ($val === null && $force == 'create') {
                    $this->$property = $this->createClass($class);
                } elseif ($val === null && $force == 'default') {
                } elseif (is_array($val) && $force == 'array') {
                    $val = array_map(function ($value) use ($class) {
                        if ($value instanceof $class) {
                            return $value;
                        } else {
                            if (is_array($value)) {
                                return $this->createClass($class, $value);
                            } else {
                                if (is_object($value)) {
                                    return $this->createClass($class, json_decode(json_encode($value), true));
                                } else {
                                    throw new Exception("array for property:{$value} dot not match in " . $class);
                                }
                            }
                        }
                    }, $val);
                    $this->$property = $val;
                } elseif ($force == 'array') {
                    $this->$property = [];
                } else {
                    $this->$property = $this->createClass($class, $val);
                }
            } else {
                throw new Exception("property:{$property} not exist in " . (static::class));
            }
        }
    }

    /**
     * @param string $class
     * @param null $arg
     *
     * @return object
     * @throws \ReflectionException
     */
    private function createClass(string $class, $arg = null)
    {
        $ref = new \ReflectionClass($class);
        return $ref->newInstance($arg);
    }

    /**
     * beanKeyMap
     * 将Bean的属性名转化为data数据键名
     *
     * @param array $array
     *
     * @return array
     */
    private function beanKeyMap(array $array): array
    {
        foreach ($this->setKeyMapping() as $dataKey => $beanKey) {
            if (array_key_exists($beanKey, $array)) {
                $array[$dataKey] = $array[$beanKey];
                unset($array[$beanKey]);
            }
        }
        return $array;
    }

    /**
     * dataKeyMap
     * 将data中的键名 转化为Bean的属性名
     *
     * @param array $array
     *
     * @return array
     */
    private function dataKeyMap(array $array): array
    {
        foreach ($this->setKeyMapping() as $dataKey => $beanKey) {
            if (array_key_exists($dataKey, $array)) {
                $array[$beanKey] = $array[$dataKey];
                unset($array[$dataKey]);
            }
        }
        return $array;
    }

    public function fill(array $data)
    {
        foreach ($data as $property => $value) {
            if (property_exists($this, $property)) {
                $this->{$property} = $value;
            }
        }
        return $this;
    }

    public function toArrayNew()
    {
        return get_object_vars($this);
    }
}
