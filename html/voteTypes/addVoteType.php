<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
verifyUser('Administrator');

if (isset($_POST['voteType'])) {
	$voteType = new VoteType();
	foreach ($_POST['voteType'] as $field=>$value) {
		$set = 'set'.ucfirst($field);
		$voteType->$set($value);
	}

	try {
		$voteType->save();
		header('Location: '.BASE_URL.'/voteTypes');
		exit();
	}
	catch(Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

$template = new Template();
$template->title = 'Add Vote Type';
$template->blocks[] = new Block('voteTypes/addVoteTypeForm.inc');
echo $template->render();
