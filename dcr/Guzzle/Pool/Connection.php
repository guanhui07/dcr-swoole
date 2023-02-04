<?php

 namespace DcrSwoole\Guzzle\Pool;

 use DcrSwoole\Pool\Connection as ConnectionPool;

 /**
  * Class Connection
  * @package Raylin666\Guzzle\Pool
  */
 class Connection extends ConnectionPool
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
