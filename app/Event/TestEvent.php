<?php
declare(strict_types = 1);

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class TestEvent
 * @see https://code.tutsplus.com/tutorials/handling-events-in-your-php-applications-using-the-symfony-eventdispatcher-component--cms-31328
 */
class TestEvent extends Event
{
    public const NAME = 'order.placed';

    protected $params;

    public function __construct($params)
    {
        $this->params = $params;
    }

    public function getParams()
    {
        return $this->params;
    }
}