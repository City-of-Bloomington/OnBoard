<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param SESSION pendingVote
 * @param GET confirm
 */
if (isset($_GET['confirm'])) {
	try {
		$_SESSION['pendingVote']->save();
		$url = $_SESSION['pendingVote']->getTopic()->getURL();
	}
	catch (Exception $e) {
		$url = BASE_URL.'/votes/updateVote.php?vote_id=?'.$_SESSION['pendingVote']->getId();
	}
	unset($_SESSION['pendingVote']);
	header("Location: $url");
	exit();
}

$template = new Template();
$template->title = 'Update Vote';
$template->blocks[] = new Block('committees/committeeInfo.inc',
								array('committee'=>$_SESSION['pendingVote']->getCommittee()));
$template->blocks[] = new Block('topics/topicInfo.inc',
								array('topic'=>$_SESSION['pendingVote']->getTopic()));
$template->blocks[] = new Block('votes/confirmDeleteInvalidVotingRecords.inc',
								array('vote'=>$_SESSION['pendingVote']));
echo $template->render();
