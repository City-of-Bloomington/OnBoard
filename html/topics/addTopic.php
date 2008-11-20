<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
verifyUser('Administrator');
$committee = new Committee($_REQUEST['committee_id']);
if (isset($_POST['topic']))
{
	$topic = new Topic();
	$topic->setCommittee($committee);
	foreach($_POST['topic'] as $field=>$value)
	{
		$set = 'set'.ucfirst($field);
		$topic->$set($value);
	}

	isset($_POST['tags']) ? $topic->setTags($_POST['tags']) : $topic->setTags();

	try
	{
		$topic->save();
		Header('Location: '.$committee->getURL());
		exit();
	}
	catch(Exception $e) { $_SESSION['errorMessages'][] = $e; }
}

$template = new Template();
$template->blocks[] = new Block('topics/addTopicForm.inc',array('committee'=>$committee));
echo $template->render();
