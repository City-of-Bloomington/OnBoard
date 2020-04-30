<?php
/**
 * @copyright 2012-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Web\Template;
use Web\Block;

$startTime = microtime(1);

include '../bootstrap.php';

// Check for routes
$p     = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$route = $ROUTES->match($p, $_SERVER);
if ($route) {
    if (isset($route->params['controller']) && isset($route->params['action'])) {
        $controller = $route->params['controller'];
        $action     = $route->params['action'];
        $c = new $controller();
        if (method_exists($c, $action)) {
            list($resource, $permission) = explode('.', $route->name);
            $role = isset($_SESSION['USER']) ? $_SESSION['USER']->role : 'Anonymous';
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
