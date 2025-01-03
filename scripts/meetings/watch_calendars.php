<?php
/**
 * Register push notification watches for all board calendars
 *
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
use Application\Models\GoogleGateway;

include '../../src/Web/bootstrap.php';

$watch_id = APPLICATION_NAME.'-79-'.uniqid();
$channel  = GoogleGateway::watch('inghamn@bloomington.in.gov', $watch_id);
print_r($channel);
