<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
verifyUser('Administrator');
if(isset($_GET['vote_id'])){
  $vote = new Vote($_GET['vote_id']); 
  $topic = $vote->getTopic();
  $votingRecordList = New VotingRecordList();
  $votingRecordList->find(array('vote_id'=>$_GET['vote_id']));

}

if (isset($_POST['votingRecord']))
{
	// print_r($_POST);
	foreach($_POST['votingRecord'] as $field=>$value)
	{

		// echo $field." ".$value."\n"; 
		// 
		// case of id field and array value
		if($field == "id" and is_array($value)){
		 	foreach($value as $index=>$val){
				$votingRecord = new VotingRecord($index);
				$votingRecord->setMemberVote($val);
				// print_r($votingRecord);
				$votingRecord->save();
			}
		}
		if($field == "vote_id"){
			$vote_id = $value;
		}
	}

	try
	{
	    if(isset($vote_id)){
	  	$vote = new Vote($vote_id); 
  	  	$topic = $vote->getTopic();
		$votingRecordList = new VotingRecordList();
 		$votingRecordList->find(array('vote_id'=>$vote_id));
		//
		//Header('Location: home.php');
		//exit();
	    }
	}
	catch(Exception $e) { $_SESSION['errorMessages'][] = $e; }
}

$template = new Template();
$template->blocks[] = new Block('committees/committeeInfo.inc',array('committee'=>$topic->getCommittee()));
$template->blocks[] = new Block('topics/topicInfo.inc',array('topic'=>$topic));
$template->blocks[] = new Block('votes/voteInfo.inc',array('vote'=>$vote));

if(isset($_GET['vote_id'])){
	$template->blocks[] = new Block('votingRecords/addVotingRecordForm.inc',array('vote'=>$vote,'votingRecordList'=>$votingRecordList));
}
else{
	$template->blocks[] = new Block('votingRecords/votingRecordList.inc',array('votingRecordList'=>$votingRecordList,'vote'=>$vote));
}
echo $template->render();