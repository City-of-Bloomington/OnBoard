<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param REQUEST return_url
 */
verifyUser('Administrator');

$user = new User();
if (isset($_POST['user']))
{
	# Both clerk and admin can edit these fields
	$fields = array('gender','firstname','lastname','email','address','city',
					'zipcode','about','race_id');

	# Only the Administrator can edit these fields
	if (userHasRole('Administrator'))
	{
		$fields[] = 'authenticationMethod';
		$fields[] = 'username';
		$fields[] = 'password';
		$fields[] = 'roles';
	}

	# Set all the fields they're allowed to edit
	foreach($fields as $field)
	{
		if (isset($_POST['user'][$field]))
		{
			$set = 'set'.ucfirst($field);
			$user->$set($_POST['user'][$field]);
		}
	}

	# Load user information from LDAP
	# Delete this statement if you're not using LDAP
	if ($user->getAuthenticationMethod() == 'LDAP')
	{
		$ldap = new LDAPEntry($user->getUsername());
		$user->setFirstname($ldap->getFirstname());
		$user->setLastname($ldap->getLastname());
		$user->setEmail($ldap->getEmail());
	}

	try
	{
		$user->save();
		Header('Location: home.php');
		exit();
	}
	catch (Exception $e) { $_SESSION['errorMessages'][] = $e; }
}

$template = new Template();

$form = new Block('users/addUserForm.inc');
$form->user = $user;
$form->return_url = $_REQUEST['return_url'];
$template->blocks[] = $form;

echo $template->render();
