<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param REQUEST voteType_id
 */
verifyUser(array('Administrator','Clerk'));

$voteType = new VoteType($_REQUEST['voteType_id']);

if (isset($_POST['voteType'])) {
	foreach ($_POST['voteType'] as $field=>$value) {
		$set = 'set'.ucfirst($field);
		$voteType->$set($value);
	}

	try {
		$voteType->save();
		header('Location: '.BASE_URL.'/voteTypes');
		exit();
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

$template = new Template();
$template->title = 'Edit Vote Type';
$template->blocks[] = new Block('voteTypes/updateVoteTypeForm.inc',array('voteType'=>$voteType));
echo $template->render();
