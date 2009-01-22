<?php
/**
 * @copyright 2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET committee_id
 */
$topic = new Topic($_GET['topic_id']);

$template = new Template();
$template->blocks[] = new Block('committees/committeeInfo.inc',array('committee'=>$topic->getCommittee()));
$template->blocks[] = new Block('topics/topicInfo.inc',array('topic'=>$topic));

$votes = new Block('votes/voteList.inc');
$votes->voteList = $topic->getVotes();
$votes->topic = $topic;
$template->blocks[] = $votes;

foreach ($topic->getVotes() as $vote)
{
	$records = new Block('votingRecords/votingRecordList.inc');
	$records->vote = $vote;
	$template->blocks[] = $records;
}

echo $template->render();
