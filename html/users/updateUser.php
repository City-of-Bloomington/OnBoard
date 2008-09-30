<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param REQUEST return_url
 */
verifyUser(array('Administrator','Clerk'));

$user = new User($_REQUEST['user_id']);

if (isset($_POST['user']))
{
	# Both clerk and admin can edit these fields
	$fields = array('firstname','lastname','email','address','city','zipcode',
				'homePhone','workPhone','about','photoPath');
	if (userHasRole('Administrator'))
	{
		$fields[] = 'authenticationMethod';
		$fields[] = 'username';
		$fields[] = 'password';
		$fields[] = 'roles';
	}
	foreach($fields as $field)
	{
		if (isset($_POST['user'][$field]))
		{
			$set = 'set'.ucfirst($field);
			$user->$set($_POST['user'][$field]);
		}
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

$form = new Block('users/updateUserForm.inc');
$form->user = $user;
$form->return_url = $_REQUEST['return_url'];
$template->blocks[] = $form;

echo $template->render();
