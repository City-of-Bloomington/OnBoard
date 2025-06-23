<?php
/**
 * @copyright 2012-2025 City of Bloomington, Indiana
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
$ROUTE   = $matcher->match($request);

if ($ROUTE) {
    foreach ($ROUTE->attributes as $k=>$v) { $_REQUEST[$k] = $v; }
    list($resource, $permission) = explode('.', $ROUTE->name);
    $role = isset($_SESSION['USER']) ? $_SESSION['USER']->getRole() : 'Anonymous';
    if (   $ACL->hasResource($resource)
        && $ACL->isAllowed($role, $resource, $permission)) {

        $controller = $ROUTE->handler;
        $c = new $controller();
        // Modern twig controllers returning a View
        if (is_callable($c)) {
            $template = $c($ROUTE->attributes);
        }
    }
    else {
        header('HTTP/1.1 403 Forbidden', true, 403);
        $_SESSION['errorMessages'][] = $role == 'Anonymous'
                                        ? 'notLoggedIn'
                                        : 'noAccessAllowed';
        $template = new \Web\Views\ForbiddenView();
    }
}

if (!isset($template)) {
    header('HTTP/1.1 404 Not Found', true, 404);
    $template = new \Web\Views\NotFoundView();
}

echo $template->render();

if ($template->outputFormat === 'html') {
    # Calculate the process time
    $endTime = microtime(true);
    $processTime = $endTime - $startTime;
    echo "<!-- Process Time: $processTime -->";
}
