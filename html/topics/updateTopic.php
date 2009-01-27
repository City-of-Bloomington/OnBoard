<?php
/**
 * @copyright 2006-2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
verifyUser('Administrator');

$topic = new Topic($_REQUEST['topic_id']);
if (isset($_POST['topic'])) {
	foreach ($_POST['topic'] as $field=>$value) {
		$set = 'set'.ucfirst($field);
		$topic->$set($value);
	}

	isset($_POST['tags']) ? $topic->setTags($_POST['tags']) : $topic->setTags();

	try {
		$topic->save();
		header('Location: '.$topic->getCommittee()->getURL());
		exit();
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

$template = new Template();
$template->title = 'Edit Topic';
$template->blocks[] = new Block('topics/updateTopicForm.inc',array('topic'=>$topic));
echo $template->render();
