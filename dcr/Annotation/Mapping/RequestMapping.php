<?php

declare(strict_types=1);

namespace DcrSwoole\Annotation\Mapping;

use Attribute;

/**
 * @Annotation
 * Class RequestMapping
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class RequestMapping extends AbstractAnnotation
{
    /** @var array|false|string[] */
    public $methods;

    /** @var mixed */
    public $path;

    /** @var array|string[] */
    public array $normal = ["GET", "POST", "PUT", "PATCH", "DELETE", "HEADER", "OPTIONS"];

    /**
     * @param ...$value
     */
    public function __construct(...$value)
    {
        $formattedValue = $this->formatParams($value);
        $this->path     = $formattedValue["path"];
        if (isset($formattedValue['methods'])) {
            if (is_string($formattedValue['methods'])) {
                // Explode a string to a array
                $this->methods = explode(',', mb_strtoupper(str_replace(' ', '', $formattedValue['methods']), 'UTF-8'));
            } else {
                $methods = [];
                foreach ($formattedValue['methods'] as $method) {
                    $methods[] = mb_strtoupper(str_replace(' ', '', $method), 'UTF-8');
                }
                $this->methods = $methods;
            }
        }
    }

    /**
     * @return array
     */
    public function setMethods(): array
    {
        $normalMethods = [];
        foreach ($this->methods as $method) {
            if (in_array($method, $this->normal, true)) {
                $normalMethods[] = $method;
            }
        }
        return $normalMethods;
    }
}
