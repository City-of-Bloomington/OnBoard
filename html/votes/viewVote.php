<?php
/**
 * @copyright 2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET committee_id
 */
$vote = new Vote($_GET['vote_id']);
$topic = $vote->getTopic();

$template = new Template();
$template->blocks[] = new Block('committees/committeeInfo.inc',array('committee'=>$topic->getCommittee()));
$template->blocks[] = new Block('topics/topicInfo.inc',array('topic'=>$topic));
$template->blocks[] = new Block('votes/voteInfo.inc',array('vote'=>$vote));

if ($vote->hasVotingRecords()) {
	$block = new Block('votingRecords/votingRecordList.inc');
	$block->votingRecordList = $vote->getVotingRecords();
	$block->vote = $vote;
	$template->blocks[] = $block;
}

echo $template->render();
