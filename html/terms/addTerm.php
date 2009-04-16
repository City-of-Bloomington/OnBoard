<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param REQUEST seat_id
 * @param REQUEST person_id
 */
verifyUser(array('Administrator','Clerk'));
$seat = new Seat($_REQUEST['seat_id']);

$template = new Template();
$template->title = 'Add Term';
$template->blocks[] = new Block('committees/committeeInfo.inc',
								array('committee'=>$seat->getCommittee()));
$template->blocks[] = new BlocK('seats/seatInfo.inc',array('seat'=>$seat));


if (isset($_REQUEST['person_id'])) {
	try {
		$person = new Person($_REQUEST['person_id']);
	}
	catch (Exception $e) {
	}

	if (isset($_POST['term'])) {
		$term = new Term();
		$term->setSeat($seat);
		$term->setPerson($person);

		foreach ($_POST['term'] as $field=>$value) {
			$set = 'set'.ucfirst($field);
			$term->$set($value);
		}

		try {
			$term->save();
			header('Location: '.$seat->getCommittee()->getURL());
			exit();
		}
		catch(Exception $e) {
			$_SESSION['errorMessages'][] = $e;
		}
	}

	$template->blocks[] = new Block('terms/addTermForm.inc',
									array('seat'=>$seat,'person'=>$person));
}
else {
	$form = new Block('people/findForm.inc');
	$form->seat_id = $seat->getId();
	$form->return_url = BASE_URL.'/terms/addTerm.php';
	$template->blocks[] = $form;
}

echo $template->render();
