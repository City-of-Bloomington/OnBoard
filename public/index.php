<?php
/**
 * @copyright 2012-2024 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);

use GuzzleHttp\Psr7\ServerRequest;
use Web\Template;
use Web\Block;

$startTime = microtime(true);

include '../src/Web/bootstrap.php';
ini_set('session.save_path', SITE_HOME.'/sessions');
ini_set('session.cookie_path', BASE_URI);
session_start();

// Check for routes
$request = ServerRequest::fromGlobals();
$matcher = $ROUTES->getMatcher();
$route   = $matcher->match($request);

if ($route) {
    $controller = $route->handler;
    $action     = $route->__get('extras')['action'];

    list($resource, $permission) = explode('.', $route->name);
    $role = isset($_SESSION['USER']) ? $_SESSION['USER']->getRole() : 'Anonymous';
    if (   $ACL->hasResource($resource)
        && $ACL->isAllowed($role, $resource, $permission)) {

        // Modern twig controllers returning a View
        if (is_callable($controller)) {
            $template = $controller($route->attributes);
        }
        // Legacy Templates and Blocks
        elseif ($controller && $action) {
            $c = new $controller();
            if (method_exists($c, $action)) {
                $template = $c->$action();
            }
        }
    }
    else {
        $template = new Template();
        header('HTTP/1.1 403 Forbidden', true, 403);
        $_SESSION['errorMessages'][] = $role == 'Anonymous'
        ? new \Exception('notLoggedIn')
        : new \Exception('noAccessAllowed');
    }
}

if (!isset($template)) {
    header('HTTP/1.1 404 Not Found', true, 404);
    $template = new Template();
    $template->blocks[] = new Block('404.inc');
}

echo $template->render();

if ($template->outputFormat === 'html') {
    # Calculate the process time
    $endTime = microtime(true);
    $processTime = $endTime - $startTime;
    echo "<!-- Process Time: $processTime -->";
}
