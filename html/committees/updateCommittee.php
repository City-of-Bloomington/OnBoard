<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET committee_id
 */
verifyUser(array('Administrator','Clerk'));

$committee = new Committee($_REQUEST['committee_id']);
if (isset($_POST['committee'])) {
	foreach ($_POST['committee'] as $field=>$value) {
		$set = 'set'.ucfirst($field);
		$committee->$set($value);
	}

	try {
		$committee->save();
		header('Location: home.php');
		exit();
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

$template = new Template();
$template->title = "Edit ".$committee->getName();
$template->blocks[] = new Block('committees/updateCommitteeForm.inc',
								array('committee'=>$committee));
echo $template->render();
