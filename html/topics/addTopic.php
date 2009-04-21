<?php
/**
 * @copyright 2006-2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param REQUEST committee_id
 */
verifyUser(array('Administrator','Clerk'));
$committee = new Committee($_REQUEST['committee_id']);
if (isset($_POST['topic'])) {
	$topic = new Topic();
	$topic->setCommittee($committee);
	foreach ($_POST['topic'] as $field=>$value) {
		$set = 'set'.ucfirst($field);
		$topic->$set($value);
	}

	isset($_POST['tags']) ? $topic->setTags($_POST['tags']) : $topic->setTags();

	try {
		$topic->save();
		$url = $committee->getURL();
		$url->tab = 'topics';
		header("Location: $url");
		exit();
	}
	catch(Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

$template = new Template();
$template->title = 'Add Legislation';
$template->blocks[] = new Block('topics/addTopicForm.inc',array('committee'=>$committee));
echo $template->render();
