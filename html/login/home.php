<?php
/**
 *	Logs a user into the system using CAS
 *
 * @copyright 2009-2010 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
if (isset($_REQUEST['return_url'])) {
	$_SESSION['return_url'] = $_REQUEST['return_url'];
}
require_once CAS.'/SimpleCAS/Autoload.php';

$options = array('hostname'=>CAS_SERVER,'uri'=>CAS_URI);
$protocol = new SimpleCAS_Protocol_Version2($options);
$client = SimpleCAS::client($protocol);
$client->forceAuthentication();

if ($client->isAuthenticated()) {
	try {
		$user = new User($client->getUsername());
		$user->startNewSession();

		if (isset($_SESSION['return_url'])) {
			header('Location: '.$_SESSION['return_url']);
		}
		else {
			header('Location: '.BASE_URL);
		}
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}
else {
	header('Location: '.BASE_URL);
}
