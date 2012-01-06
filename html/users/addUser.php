<?php
/**
 * @copyright 2006-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET person_id
 */
verifyUser('Administrator');
if (isset($_REQUEST['person_id'])) {
	try {
		$person = new Person($_REQUEST['person_id']);
	}
	catch (Exception $e) {
	}
}

if (isset($_POST['user'])) {

	$user = new User();
	foreach ($_POST['user'] as $field=>$value) {
		$set = 'set'.ucfirst($field);
		$user->$set($value);
	}

	if (isset($person)) {
		$user->setPerson_id($person->getId());
	}
	else {
		// Load their information from LDAP
		if ($user->getAuthenticationMethod() != 'local') {
			try {
				$externalIdentity = $user->getAuthenticationMethod();
				$identity = new $externalIdentity($user->getUsername());
				try {
					$person = new Person($identity->getEmail());
				}
				catch (Exception $e) {
					$person = new Person();
					$person->setFirstname($identity->getFirstname());
					$person->setLastname($identity->getLastname());
					$person->setEmail($identity->getEmail());
					$person->save();
				}
				$user->setPerson($person);
			}
			catch (Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}
	}

	try {
		$user->save();
		header('Location: '.BASE_URL.'/users');
		exit();
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

$template = new Template();
$template->title = 'Create a user account';
$template->blocks[] = new Block('users/addUserForm.inc');
if (isset($person)) {
	$template->blocks[] = new Block('people/personInfo.inc',array('person'=>$person));
}
echo $template->render();
