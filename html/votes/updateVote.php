<?php
/**
 * @copyright 2006-2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET vote_id
 */
verifyUser(array('Administrator','Clerk'));

$vote = new Vote($_REQUEST['vote_id']);

if (isset($_POST['vote'])) {
	foreach ($_POST['vote'] as $field=>$value) {
		$set = 'set'.ucfirst($field);
		$vote->$set($value);
	}

	// If there are invalid votingRecords, make sure the user knows they're
	// going to be deleting a bunch of votingRecords when they save
	if ($vote->hasInvalidVotingRecords()) {
		$_SESSION['pendingVote'] = $vote;
		header('Location: confirmDeleteInvalidVotingRecords.php');
		exit();
	}

	try {
		$vote->save();
		header('Location: '.$vote->getTopic()->getURL());
		exit();
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
	exit();
}
$topic = $vote->getTopic();

$template = new Template();
$template->title = 'Update Vote';
$template->blocks[] = new Block('committees/committeeInfo.inc',
								array('committee'=>$topic->getCommittee()));
$template->blocks[] = new Block('topics/topicInfo.inc',array('topic'=>$topic));

$template->blocks[] = new Block('votes/updateVoteForm.inc',array('vote'=>$vote));
echo $template->render();
