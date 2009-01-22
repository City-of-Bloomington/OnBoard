<?php
/**
 * @copyright 2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param REQUEST member_id
 */
verifyuser(array('Administrator','Clerk'));

$member = new Member($_REQUEST['member_id']);
if (isset($_POST['member']))
{
	foreach ($_POST['member'] as $field=>$value)
	{
		$set = 'set'.ucfirst($field);
		$member->$set($value);
	}

	# Update the User's personal information
	$user = $member->getUser();

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
		Header('Location: '.$member->getSeat()->getURL());
		exit();
	}
	catch(Exception $e) { $_SESSION['errorMessages'][] = $e; }
}

$template = new Template();
$template->blocks[] = new Block('committees/committeeInfo.inc',array('committee'=>$member->getSeat()->getCommittee()));
$template->blocks[] = new Block('seats/seatInfo.inc',array('seat'=>$member->getSeat()));
$template->blocks[] = new Block('members/updateMemberForm.inc',array('member'=>$member));
echo $template->render();
