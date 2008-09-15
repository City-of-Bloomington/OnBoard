<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
verifyUser('Administrator');

$topic = new Topic($_REQUEST['id']);
if (isset($_POST['topic']))
{
	foreach($_POST['topic'] as $field=>$value)
	{
		$set = 'set'.ucfirst($field);
		$topic->$set($value);
	}

	try
	{
		$topic->save();
		Header('Location: home.php');
		exit();
	}
	catch (Exception $e) { $_SESSION['errorMessages'][] = $e; }
}

$template = new Template();
$template->blocks[] = new Block('topics/updateTopicForm.inc',array('topic'=>$topic));
echo $template->render();