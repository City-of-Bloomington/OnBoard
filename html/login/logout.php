<?php
/**
 * Logs a user out of the system
 *
 * @copyright 2008-2010 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
if (isset($_COOKIE[CAS_COOKIE])) {
	setcookie(CAS_COOKIE,'true',time()-3600,'/',CAS_DOMAIN);

	require_once CAS.'/SimpleCAS/Autoload.php';

	$options = array('hostname'=>CAS_SERVER,'uri'=>CAS_URI);
	$protocol = new SimpleCAS_Protocol_Version2($options);
	$client = SimpleCAS::client($protocol);
	$client->logout(BASE_URL);
}

session_destroy();
header('Location: '.BASE_URL);
