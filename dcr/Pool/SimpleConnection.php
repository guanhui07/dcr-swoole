<?php

namespace  DcrSwoole\Pool;

/**
 * Class SimpleConnection
 * @package Raylin666\Pool
 */
class SimpleConnection extends Connection
{
    /**
     * @return mixed
     */
    public function getActiveConnection()
    {
        // TODO: Implement getActiveConnection() method.

        return $this->reconnect();
    }
}
