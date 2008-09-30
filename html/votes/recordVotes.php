<?php
/**
 * @copyright Copyright (C) 2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET vote_id
 */
verifyUser(array('Administrator','Webmaster'));

$vote = new Vote($_REQUEST['vote_id']);
if (isset($_POST['votingRecord']))
{
	$vote->setVotingRecords($_POST['votingRecord']);
	try
	{
		$vote->save();
		Header('Location: '.$vote->getURL());
		exit();
	}
	catch (Exception $e) { $_SESSION['errorMessages'][] = $e; }
}

$template = new Template();
$template->blocks[] = new Block('committees/committeeInfo.inc',array('committee'=>$vote->getCommittee()));
$template->blocks[] = new Block('topics/topicInfo.inc',array('topic'=>$vote->getTopic()));
$template->blocks[] = new Block('votes/voteInfo.inc',array('vote'=>$vote));
$template->blocks[] = new Block('votes/recordVotesForm.inc',array('vote'=>$vote));
echo $template->render();