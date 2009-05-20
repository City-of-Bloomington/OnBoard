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

$template->blocks[] = new Block('topics/breadcrumbs.inc',array('topic'=>$topic));
$template->blocks[] = new Block('committees/committeeInfo.inc',
								array('committee'=>$topic->getCommittee()));
$template->blocks[] = new Block('topics/topicInfo.inc',array('topic'=>$topic));
$template->blocks[] = new Block('votes/voteList.inc',
								array('topic'=>$topic,'voteList'=>$topic->getVotes()));
foreach ($topic->getVotes() as $vote) {
	$template->blocks[] = new Block('votingRecords/votingRecordList.inc',array('vote'=>$vote));
}

echo $template->render();
