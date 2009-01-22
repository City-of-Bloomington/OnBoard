<?php
/**
 * @copyright 2006-2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @param REQUEST seat_id
 * @param REQUEST username
 * @param REQUEST user_id
 */
verifyUser(array('Administrator','Clerk'));

#--------------------------------------------------------------------
# Load all the data we need from the database
#--------------------------------------------------------------------
$seat = new Seat($_REQUEST['seat_id']);
if (isset($_REQUEST['user_id']))
{
	try { $user = new User($_REQUEST['user_id']); }
	catch (Exception $e) { $_SESSION['errorMessages'][] = $e; }
}

#--------------------------------------------------------------------
# Handle any user-posted data to create the new member
#--------------------------------------------------------------------
if (isset($_POST['member']))
{
	$member = new Member();
	$member->setSeat($seat);
	$member->setUser($user);

	foreach ($_POST['member'] as $field=>$value)
	{
		$set = 'set'.ucfirst($field);
		$member->$set($value);
	}

	# Both clerk and admin can edit these fields
	$fields = array('gender','firstname','lastname','email','address','city',
					'zipcode','about','race_id','birthdate','phoneNumbers','privateFields');

	# Set all the fields they're allowed to edit
	foreach ($fields as $field)
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
		$member->save();
		Header('Location: '.$seat->getURL());
		exit();
	}
	catch(Exception $e) { $_SESSION['errorMessages'][] = $e; }
}

#--------------------------------------------------------------------
# Render the web page
#--------------------------------------------------------------------
$template = new Template();
$template->blocks[] = new Block('committees/committeeInfo.inc',array('committee'=>$seat->getCommittee()));
$template->blocks[] = new Block('seats/seatInfo.inc',array('seat'=>$seat));

if (!isset($user))
{
	$findUserForm = new Block('members/findUserForm.inc');
	$findUserForm->seat = $seat;
	$template->blocks[] = $findUserForm;
}
else
{
	$addMemberForm = new Block('members/addMemberForm.inc');
	$addMemberForm->seat = $seat;
	$addMemberForm->user = $user;
	$template->blocks[] = $addMemberForm;
}
echo $template->render();
