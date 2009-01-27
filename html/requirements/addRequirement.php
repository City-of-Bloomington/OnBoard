<?php
/**
 * @copyright 2006-2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
verifyUser(array('Administrator','Clerk'));

if (isset($_POST['requirement'])) {
	$requirement = new Requirement();
	foreach ($_POST['requirement'] as $field=>$value) {
		$set = 'set'.ucfirst($field);
		$requirement->$set($value);
	}

	try {
		$requirement->save();
		header('Location: '.BASE_URL.'/requirements');
		exit();
	}
	catch(Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

$template = new Template();
$template->title = 'Add Requirement';
$template->blocks[] = new Block('requirements/addRequirementForm.inc');
echo $template->render();
