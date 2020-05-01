<?php
/**
 * @copyright 2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);

use Aura\Router\Route;
use PHPUnit\Framework\TestCase;

class RoutesTest extends TestCase
{
    public function routes()
    {
        global $ROUTES;

        $routes = [];
        foreach ($ROUTES->getRoutes() as $r) {
            $routes[] = [$r];
        }
        return $routes;
    }

    /**
     * @dataProvider routes
     */
    public function testRoutes(Route $r)
    {
        list($resource, $action) = explode('.', $r->name);
        $uri = $action == 'index' ? "/$resource" : "/$resource/$action";

        if ($resource == 'home'    ) { $uri = '/'; }
        if ($resource == 'callback') { $uri = '/callback'; }

        $controller = $r->values['controller'];

        $this->assertEquals($uri, $r->path, 'Name does not match uri');
        $this->assertEquals($controller,
                            'Application\Controllers\\'.ucfirst($resource).'Controller',
                            'Controller does not match route name');
        $c = new $controller();
        $this->assertTrue(method_exists($c, $action));
    }
}
