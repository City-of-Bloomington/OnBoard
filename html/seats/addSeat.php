<?php
/**
 * @copyright 2008-2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param REQUEST committee_id
 */
verifyUser(array('Administrator','Clerk'));

$committee = new Committee($_REQUEST['committee_id']);
if (isset($_POST['seat'])) {
	$seat = new Seat();
	$seat->setCommittee($committee);

	foreach ($_POST['seat'] as $field=>$value) {
		$set = 'set'.ucfirst($field);
		$seat->$set($value);
	}

	try {
		$seat->save();
		header('Location: '.$committee->getURL());
		exit();
	}
	catch(Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

$template = new Template();
$template->title = 'Add Seat';
$template->blocks[] = new Block('committees/committeeInfo.inc',array('committee'=>$committee));
$template->blocks[] = new Block('seats/addSeatForm.inc',array('committee'=>$committee));
echo $template->render();
