<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param REQUEST race_id
 */

verifyUser(array('Administrator','Clerk'));

$race = new Race($_REQUEST['race_id']);
if (isset($_POST['race'])) {
	foreach ($_POST['race'] as $field=>$value) {
		$set = 'set'.ucfirst($field);
		$race->$set($value);
	}

	try {
		$race->save();
		header('Location: '.BASE_URL.'/races');
		exit();
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

$template = new Template();
$template->title = 'Edit Race';
$template->blocks[] = new Block('races/updateRaceForm.inc',array('race'=>$race));
echo $template->render();
