<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
verifyUser('Administrator');

$votingRecord = new VotingRecord($_REQUEST['id']);
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
		Header('Location: home.php');
		exit();
	}
	catch (Exception $e) { $_SESSION['errorMessages'][] = $e; }
}

$template = new Template();
$template->blocks[] = new Block('votingRecords/updateVotingRecordForm.inc',array('votingRecord'=>$votingRecord));
echo $template->render();