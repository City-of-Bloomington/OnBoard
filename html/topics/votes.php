<?php
/**
 * @copyright 2008-2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET topic_id
 */
$topic = new Topic($_GET['topic_id']);

$template = new Template();
$template->title = $topic->getDescription();

$template->blocks[] = new Block('committees/committeeInfo.inc',
								array('committee'=>$topic->getCommittee()));

if ($template->outputFormat == 'html') {
	$template->blocks[] = new Block('topics/tabs.inc',
								array('topic'=>$topic,'currentTab'=>'votes'));

$votes = new Block('votes/voteList.inc');
$votes->voteList = $topic->getVotes();
$votes->topic = $topic;
$template->blocks[] = $votes;

foreach ($topic->getVotes() as $vote) {
	$records = new Block('votingRecords/votingRecordList.inc');
	$records->vote = $vote;
	$template->blocks[] = $records;
}
}

echo $template->render();
