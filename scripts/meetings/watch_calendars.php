<?php
/**
 * Register push notification watches for all board calendars
 *
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);

use Application\Models\Committee;
use Application\Models\GoogleGateway;


include '../../src/Web/bootstrap.php';

$c = new Committee(79);

$watch_id = APPLICATION_NAME."-{$c->getId()}-".uniqid();
$channel  = GoogleGateway::watch($c->getCalendarId(), $watch_id);
print_r($channel);
