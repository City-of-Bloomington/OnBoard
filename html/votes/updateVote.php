<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
verifyUser('Administrator');

$vote = new Vote($_REQUEST['id']);
if (isset($_POST['vote']))
{
	foreach($_POST['vote'] as $field=>$value)
	{
		$set = 'set'.ucfirst($field);
		$vote->$set($value);
	}

	try
	{
		$vote->save();
		Header('Location: home.php');
		exit();
	}
	catch (Exception $e) { $_SESSION['errorMessages'][] = $e; }
}

$template = new Template();
$template->blocks[] = new Block('votes/updateVoteForm.inc',array('vote'=>$vote));
echo $template->render();