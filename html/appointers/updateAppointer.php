<?php
/**
 * @copyright 2006-2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham
 */
verifyUser(array('Administrator','Clerk'));

$appointer = new Appointer($_REQUEST['appointer_id']);
if (isset($_POST['appointer'])) {
	foreach ($_POST['appointer'] as $field=>$value) {
		$set = 'set'.ucfirst($field);
		$appointer->$set($value);
	}

	try {
		$appointer->save();
		header('Location: home.php');
		exit();
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

$template = new Template();
$template->title = 'Edit Appointer';
$template->blocks[] = new Block('appointers/updateAppointerForm.inc',
								array('appointer'=>$appointer));
echo $template->render();
