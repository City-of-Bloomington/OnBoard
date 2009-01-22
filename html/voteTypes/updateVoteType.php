<?php
/**
 * @copyright 2006-2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
verifyUser('Administrator');

$voteType = new VoteType($_REQUEST['id']);
if (isset($_POST['voteType']))
{
	foreach ($_POST['voteType'] as $field=>$value)
	{
		$set = 'set'.ucfirst($field);
		$voteType->$set($value);
	}

	try
	{
		$voteType->save();
		// Header('Location: home.php');
		// exit();
	}
	catch (Exception $e) { $_SESSION['errorMessages'][] = $e; }
}

$template = new Template();
$template->blocks[] = new Block('voteTypes/updateVoteTypeForm.inc',array('voteType'=>$voteType));
echo $template->render();