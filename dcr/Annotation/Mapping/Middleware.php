<?php

declare(strict_types=1);

namespace DcrSwoole\Annotation\Mapping;

use Attribute;

/**
 * @Annotation
 * @Target({"ALL"})
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Middleware extends AbstractAnnotation
{

    public array|string $middleware = '';

    public function __construct(...$value)
    {
        $this->bindMainProperty('middleware', $value);
        $this->middleware = $value;
    }
}
