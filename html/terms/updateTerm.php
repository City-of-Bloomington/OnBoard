<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET term_id
 * @param REQUEST return_url
 */
verifyUser(array('Administrator','Clerk'));

$term = new Term($_REQUEST['term_id']);

$return_url = isset($_REQUEST['return_url'])
			? $_REQUEST['return_url']
			: $term->getSeat()->getCommittee()->getURL();


if (isset($_POST['term'])) {
	foreach ($_POST['term'] as $field=>$value) {
		$set = 'set'.ucfirst($field);
		$term->$set($value);
	}

	// If there are invalid votingRecords, make sure the user knows they're
	// going to be deleting a bunch of votingRecords when they save
	if ($term->hasInvalidVotingRecords()) {
		$_SESSION['pendingTerm'] = $term;
		$url = new URL(BASE_URL.'/terms/confirmDeleteInvalidVotingRecords.php');
		$url->return_url = $_REQUEST['return_url'];
		header("Location: $url");
		exit();
	}

	try {
		$term->save();
		header('Location: '.$return_url);
		exit();
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

$template = new Template();
$template->title = 'Edit Term';
$template->blocks[] = new Block('committees/committeeInfo.inc',
								array('committee'=>$term->getSeat()->getCommittee()));
$template->blocks[] = new BlocK('seats/seatInfo.inc',array('seat'=>$term->getSeat()));
$template->blocks[] = new Block('terms/updateTermForm.inc',
								array('term'=>$term,'return_url'=>$return_url));
echo $template->render();
