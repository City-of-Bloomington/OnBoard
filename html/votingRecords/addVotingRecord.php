<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
verifyUser('Administrator');
if(isset($_REQUEST['vote_id'])){
  $vote = new Vote($_GET['vote_id']); 
}
else
 $vote = new Vote();
if (isset($_POST['votingRecord']))
{
	$votingRecord = new VotingRecord();
	foreach($_POST['votingRecord'] as $field=>$value)
	{
		$set = 'set'.ucfirst($field);
		$votingRecord->$set($value);
	}

	try
	{
		$votingRecord->save();
		//Header('Location: home.php');
		//exit();
	}
	catch(Exception $e) { $_SESSION['errorMessages'][] = $e; }
}

$template = new Template();
	if(isset($_REQUEST['vote_id'])){
	$template->blocks[] = new Block('votingRecords/addVotingRecordForm.inc',array('vote'=>$vote));
}
else{
	$votingRecordList = new VotingRecordList();
	$votingRecordList->find();
	$template->blocks[] = new Block('votingRecords/votingRecordList.inc',array('votingRecordList'=>$votingRecordList,'vote'=>$vote));
}
echo $template->render();