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
if (preg_match('|'.BASE_URI.'(/([a-zA-Z0-9]+))?(/([a-zA-Z0-9]+))?|',$_SERVER['REQUEST_URI'],$matches)) {
	$resource = isset($matches[2]) ? $matches[2] : 'index';
	$action   = isset($matches[4]) ? $matches[4] : 'index';
}

// Create the default Template
$template = !empty($_REQUEST['format'])
	? new Template('default',$_REQUEST['format'])
	: new Template('default');

// Execute the Controller::action()
if (isset($resource) && isset($action) && $ZEND_ACL->hasResource($resource)) {
    $controller = 'Application\Controllers\\'.ucfirst($resource).'Controller';
    $c = new $controller($template);
    if (method_exists($c, $action)) {
        $role = isset($_SESSION['USER']) ? $_SESSION['USER']->getRole() : 'Anonymous';
        if ($ZEND_ACL->isAllowed($role, $resource, $action)) {
            $c->$action();
        }
        else {
            header('HTTP/1.1 403 Forbidden', true, 403);
            $_SESSION['errorMessages'][] = $role == 'Anonymous'
                ? new \Exception('notLoggedIn')
                : new \Exception('noAccessAllowed');
        }
    }
    else {
        header('HTTP/1.1 404 Not Found', true, 404);
        $template->blocks[] = new Block('404.inc');
    }
}
else {
	header('HTTP/1.1 404 Not Found', true, 404);
	$template->blocks[] = new Block('404.inc');
}

echo $template->render();

if ($template->outputFormat === 'html') {
    # Calculate the process time
    $endTime = microtime(1);
    $processTime = $endTime - $startTime;
    echo "<!-- Process Time: $processTime -->";
}
