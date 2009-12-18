<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
verifyUser(array('Administrator','Clerk'));

if (isset($_POST['topicType'])) {
	$topicType = new TopicType();
	foreach ($_POST['topicType'] as $field=>$value) {
		$set = 'set'.ucfirst($field);
		$topicType->$set($value);
	}

	try {
		$topicType->save();
		header('Location: '.BASE_URL.'/topicTypes');
		exit();
	}
	catch(Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

$template = new Template();
$template->title = 'Add new topic type';
$template->blocks[] = new Block('topicTypes/addTopicTypeForm.inc');
echo $template->render();
