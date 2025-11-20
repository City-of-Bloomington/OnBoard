<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
use PHPUnit\Framework\TestCase;

class ControllerTest extends TestCase
{
    public function testControllers(): void
    {
        global $ROUTES;

        foreach ($ROUTES->getMap()->getRoutes() as $r) {
            $class = $r->handler;
            $c = new $class();
            $this->assertInstanceOf($class, $c);
        }
    }
}
