<?php
/**
 * @copyright 2006-2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param REQUEST seat_id
 */
verifyUser(array('Administrator','Clerk'));

$seat = new Seat($_REQUEST['seat_id']);
if (isset($_POST['seat'])) {
	foreach ($_POST['seat'] as $field=>$value) {
		$set = 'set'.ucfirst($field);
		$seat->$set($value);
	}

	try {
		$seat->save();
		header('Location: '.$seat->getURL());
		exit();
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

$template = new Template();
$template->title = 'Edit Seat';
$template->blocks[] = new Block('committees/committeeInfo.inc',
								array('committee'=>$seat->getCommittee()));
$template->blocks[] = new Block('seats/updateSeatForm.inc',array('seat'=>$seat));
echo $template->render();
