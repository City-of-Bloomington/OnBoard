<?php
/**
 * @copyright 2012-2024 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
use GuzzleHttp\Psr7\ServerRequest;
use Web\Template;
use Web\Block;

$startTime = microtime(1);

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

    if ($controller && $action) {
        $c = new $controller();
        if (method_exists($c, $action)) {
            list($resource, $permission) = explode('.', $route->name);
            $role = isset($_SESSION['USER']) ? $_SESSION['USER']->getRole() : 'Anonymous';
            if (   $ACL->hasResource($resource)
                && $ACL->isAllowed($role, $resource, $permission)) {

                $template = $c->$action();
            }
            else {
                $template = new Template();
                header('HTTP/1.1 403 Forbidden', true, 403);
                $_SESSION['errorMessages'][] = $role == 'Anonymous'
                    ? new \Exception('notLoggedIn')
                    : new \Exception('noAccessAllowed');
            }
        }
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
    $endTime = microtime(1);
    $processTime = $endTime - $startTime;
    echo "<!-- Process Time: $processTime -->";
}
