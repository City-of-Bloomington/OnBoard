<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
verifyUser(array('Administrator','Clerk'));

$seat = new Seat($_REQUEST['seat_id']);
if (isset($_POST['seat']))
{
	foreach($_POST['seat'] as $field=>$value)
	{
		$set = 'set'.ucfirst($field);
		$seat->$set($value);
	}

	try
	{
		$seat->save();
		Header('Location: '.$seat->getCommittee()->getURL());
		exit();
	}
	catch (Exception $e) { $_SESSION['errorMessages'][] = $e; }
}

$template = new Template();
$template->blocks[] = new Block('committees/committeeInfo.inc',array('committee'=>$seat->getCommittee()));
$template->blocks[] = new Block('seats/updateSeatForm.inc',array('seat'=>$seat));
echo $template->render();
