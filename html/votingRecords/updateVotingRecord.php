<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
verifyUser('Administrator');
if(isset($_REQUEST['id'])){
	$votingRecord = new VotingRecord($_REQUEST['id']);
	$vote = $votingRecord->getVote();
	$topic = $vote->getTopic();
}
if (isset($_POST['votingRecord']))
{
	foreach($_POST['votingRecord'] as $field=>$value)
	{
		$set = 'set'.ucfirst($field);
		$votingRecord->$set($value);
	}

	try
	{
		$votingRecord->save();
		$vote = $votingRecord->getVote();	
		$topic = $vote->getTopic();	
		// Header('Location: home.php');
		// exit();
	}
	catch (Exception $e) { $_SESSION['errorMessages'][] = $e; }
}

$template = new Template();

$template->blocks[] = new Block('committees/committeeInfo.inc',array('committee'=>$topic->getCommittee()));
$template->blocks[] = new Block('topics/topicInfo.inc',array('topic'=>$topic));
$template->blocks[] = new Block('votes/voteInfo.inc',array('vote'=>$vote));

if (!isset($_POST['votingRecord'])){
	$template->blocks[] = new Block('votingRecords/updateVotingRecordForm.inc',array('votingRecord'=>$votingRecord, 'vote'=>$vote));

}
else{
	$votingRecordList = new VotingRecordList();
	$votingRecordList->find();
	$template->blocks[] = new Block('votingRecords/votingRecordList.inc',array('votingRecordList'=>$votingRecordList,'vote'=>$vote));
}
echo $template->render();