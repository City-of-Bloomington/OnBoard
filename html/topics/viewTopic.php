<?php
/**
 * @copyright Copyright (C) 2008 City of Bloomington, Indiana. All rights reserved.
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
if($topic->hasVotes()){
	$votelist = $topic->getVotes();
	foreach($voteList as $vote){
	if($vote->hasVotingRecords()){
		$votingRecords = new Block('votingRecords/votingRecordList.inc');
		$votingRecords->votingRecordList = $vote->getVotingRecords();
		$template->blocks[] = $votingRecords;
	}
}
echo $template->render();
