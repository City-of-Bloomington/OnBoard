<?php
/**
 * @copyright Copyright (C) 2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param REQUEST member_id
 */
verifyuser(array('Administrator','Clerk'));

$member = new Member($_REQUEST['member_id']);
if (isset($_POST['member']))
{
	foreach($_POST['member'] as $field=>$value)
	{
		$set = 'set'.ucfirst($field);
		$member->$set($value);
	}

	# Update the User's personal information
	$user = $member->getUser();
	$user->setFirstname($_POST['user']['firstname']);
	$user->setLastname($_POST['user']['lastname']);
	$user->setEmail($_POST['user']['email']);
	$user->setAddress($_POST['user']['address']);
	$user->setCity($_POST['user']['city']);
	$user->setZipCode($_POST['user']['zipcode']);
	$user->setHomePhone($_POST['user']['homePhone']);
	$user->setWorkPhone($_POST['user']['workPhone']);
	$user->setAbout($_POST['user']['about']);
	$user->setPhotoPath($_POST['user']['photoPath']);

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
