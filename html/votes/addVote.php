<?php
/**
 * @copyright 2006-2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @param GET topic_id
 */
verifyUser('Administrator');
$topic = new Topic($_REQUEST['topic_id']);
if (isset($_POST['vote']))
{
	$vote = new Vote();
	$vote->setTopic($topic);

	foreach ($_POST['vote'] as $field=>$value)
	{
		$set = 'set'.ucfirst($field);
		$vote->$set($value);
	}

	try
	{
		$vote->save();
		Header('Location: '.$topic->getURL());
		exit();
	}
	catch(Exception $e) { $_SESSION['errorMessages'][] = $e; }
}


$template = new Template();
$template->blocks[] = new Block('committees/committeeInfo.inc',array('committee'=>$topic->getCommittee()));
$template->blocks[] = new Block('topics/topicInfo.inc',array('topic'=>$topic));
$template->blocks[] = new Block('votes/addVoteForm.inc',array('topic'=>$topic));


echo $template->render();