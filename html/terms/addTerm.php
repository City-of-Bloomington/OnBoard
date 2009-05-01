<?php
/**
 * Implements a mult-step process for adding a new term
 *
 * A Term is a person filling a seat.  We need to make sure we're getting a valid
 * person.  We want to minimize duplicate records for the same person.
 *
 * Force them to do a Find in the system for the person
 *
 * If they don't find that person, we let them add a new person record
 *
 * Wait until we have a valid person before we show the actual addTermForm.
 *
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param REQUEST seat_id
 * @param REQUEST person_id
 */
verifyUser(array('Administrator','Clerk'));

// A seat is always required.  Some of the forms do not keep track of
// the seat_id, so we're storing the seat in the session
if (isset($_REQUEST['seat_id'])) {
	$seat = new Seat($_REQUEST['seat_id']);
	$_SESSION['seat_id'] = $seat;
}
else {
	$seat = $_SESSION['seat_id'];
}


// We use the presence of person_id to determine if they've done a search or not
if (!isset($_REQUEST['person_id'])) {
	// They have not even bothered to do a search for the person yet
	// Show the find form
	$currentStep = new Block('terms/findPersonForm.inc');
	$currentStep->seat_id = $seat->getId();
}
else {
	// Passing in an empty or invalid person means they did a search, but weren't able
	// to find anyone in the system like that.
	// This should result in us bringing up the form to add a new person
	if ($_REQUEST['person_id']) {
		try {
			$person = new Person($_REQUEST['person_id']);
		}
		catch (Exception $e) {
		}
	}

	if (!isset($person)) {
		// They don't have a valid person yet, they need to add a new person
		$person = new Person();
		if (isset($_POST['person'])) {
			foreach ($_POST['person'] as $field=>$value) {
				$set = 'set'.ucfirst($field);
				$person->$set($value);
			}

			try {
				$person->save();
				$url = new URL(BASE_URL.'/terms/addTerm.php');
				$url->seat_id = $seat->getId();
				$url->person_id = $person->getId();
				header('Location: '.$url);
				exit();
			}
			catch(Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}
		$currentStep = new Block('people/updatePersonForm.inc',
								 array('person'=>$person,'title'=>'New Person'));
	}
	else {
		// If we've loadded a valid person, they're allowed to post a new term
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
				header('Location: '.$seat->getURL());
				exit();
			}
			catch(Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}
		$currentStep = new Block('terms/addTermForm.inc',array('seat'=>$seat,'person'=>$person));
	}
}

$template = new Template();
$template->title = 'Add Term';
$template->blocks[] = new Block('terms/breadcrumbs.inc',array('seat'=>$seat));
$template->blocks[] = new Block('seats/seatInfoCondensed.inc',array('seat'=>$seat));
$template->blocks[] = $currentStep;
echo $template->render();
