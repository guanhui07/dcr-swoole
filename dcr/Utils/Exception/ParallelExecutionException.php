<?php

declare(strict_types=1);

namespace DcrSwoole\Utils\Exception;

use RuntimeException;

class ParallelExecutionException extends RuntimeException
{
    /**
     * @var array
     */
    private $results;

    /**
     * @var array
     */
    private $throwables;

    public function getResults()
    {
        return $this->results;
    }

    public function setResults(array $results)
    {
        $this->results = $results;
    }

    public function getThrowables()
    {
        return $this->throwables;
    }

    public function setThrowables(array $throwables)
    {
        return $this->throwables = $throwables;
    }
}
