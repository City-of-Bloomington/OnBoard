<?php
/**
 * @copyright 2006-2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
verifyUser('Administrator');

if (isset($_POST['topicType']))
{
	$topicType = new TopicType();
	foreach ($_POST['topicType'] as $field=>$value)
	{
		$set = 'set'.ucfirst($field);
		$topicType->$set($value);
	}

	try
	{
		$topicType->save();
		Header('Location: '.BASE_URL.'/topicTypes');
		exit();
	}
	catch(Exception $e) { $_SESSION['errorMessages'][] = $e; }
}

$template = new Template();
$template->blocks[] = new Block('topicTypes/addTopicTypeForm.inc');
echo $template->render();