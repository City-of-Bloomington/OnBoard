<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
verifyUser(array('Administrator','Clerk'));

$person = new Person();

if (isset($_POST['person'])) {
	foreach ($_POST['person'] as $field=>$value) {
		$set = 'set'.ucfirst($field);
		$person->$set($value);
	}

	try {
		$person->save();
		header('Location: '.BASE_URL.'/people');
		exit();
	}
	catch(Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

$template = new Template();
$template->title = 'Add a Person';
$template->blocks[] = new Block('people/updatePersonForm.inc',
								array('person'=>$person,'title'=>'New Person'));
echo $template->render();
