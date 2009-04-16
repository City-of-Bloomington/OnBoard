<?php
/**
 *	Logs a user into the system.
 *
 *	A logged in user will have a $_SESSION['USER']
 *								$_SESSION['IP_ADDRESS']
 *
 * @copyright 2006-2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
try {
	$user = new User($_POST['username']);

	if ($user->authenticate($_POST['password'])) {
		$user->startNewSession();
	}
	else {
		throw new Exception('wrongPassword');
	}
}
catch (Exception $e) {
	$_SESSION['errorMessages'][] = $e;
	header('Location: '.BASE_URL);
	exit();
}

// The user has successfully logged in.  Redirect them wherever you like
header('Location: '.BASE_URL);
