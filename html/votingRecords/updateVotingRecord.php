<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
verifyUser('Administrator');

$votingRecord = new VotingRecord($_REQUEST['id']);
$vote = $votingRecord->getVote();
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
		// Header('Location: home.php');
		// exit();
	}
	catch (Exception $e) { $_SESSION['errorMessages'][] = $e; }
}

$template = new Template();

if (!isset($_POST['votingRecord'])){
	$template->blocks[] = new Block('votingRecords/updateVotingRecordForm.inc',array('votingRecord'=>$votingRecord, 'vote'=>$vote));
}
else{
	$votingRecordList = new VotingRecordList();
	$votingRecordList->find();
	$template->blocks[] = new Block('votingRecords/votingRecordList.inc',array('votingRecordList'=>$votingRecordList,'vote'=>$vote));
}
echo $template->render();