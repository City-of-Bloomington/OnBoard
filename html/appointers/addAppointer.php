<?php
/**
 * @copyright 2006-2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
verifyUser(array('Administrator','Clerk'));

if (isset($_POST['appointer'])) {
	$appointer = new Appointer();
	foreach ($_POST['appointer'] as $field=>$value) {
		$set = 'set'.ucfirst($field);
		$appointer->$set($value);
	}

	try {
		$appointer->save();
		header('Location: home.php');
		exit();
	}
	catch(Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

$template = new Template();
$template->title = 'Add Appointer';
$template->blocks[] = new Block('appointers/addAppointerForm.inc');
echo $template->render();
