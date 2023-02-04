<?php
declare(strict_types = 1);

namespace DcrTest;

use app\Repository\TestRepository;
use PHPUnit\Framework\TestCase;

/**
 * Class ValidatorsTest
 * @package DcrTest
 */
class ValidatorsTest extends TestCase
{

    public function testBool(): void
    {

        $this->assertFalse(false);
        $this->assertTrue(TestRepository::test2());
    }

}
