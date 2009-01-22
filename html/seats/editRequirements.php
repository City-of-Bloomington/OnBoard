<?php
/**
 * @copyright 2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param REQUEST seat_id
 */
verifyUser(array('Administrator','Clerk'));

$seat = new Seat($_REQUEST['seat_id']);
try
{
	# User Posted a new Requirement
	if (isset($_POST['text']) && trim($_POST['text']))
	{
		$requirement = new Requirement();
		$requirement->setText($_POST['text']);
		$requirement->save();
	}
	# User selected an existing Requirement
	elseif (isset($_POST['requirement_id']))
	{
		$requirement = new Requirement($_POST['requirement_id']);
	}

	if (isset($requirement)) { $seat->addRequirement($requirement); }
}
catch (Exception $e) { $_SESSION['errorMessages'][] = $e; }


$template = new Template();
$template->blocks[] = new Block('committees/committeeInfo.inc',array('committee'=>$seat->getCommittee()));
$template->blocks[] = new Block('seats/seatInfo.inc',array('seat'=>$seat));
$template->blocks[] = new Block('seats/manageRequirementsForm.inc',array('seat'=>$seat));
echo $template->render();