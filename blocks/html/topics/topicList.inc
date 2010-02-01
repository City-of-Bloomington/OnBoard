<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param TopicList $this->topicList
 */
foreach ($this->topicList as $topic) {
	$editButton = '';
	if (userHasRole(array('Administrator','Clerk'))) {
		$url = new URL(BASE_URL.'/topics/updateTopic.php');
		$url->topic_id = $topic->getId();
		$editButton = "<button type=\"button\" class=\"edit\" onclick=\"document.location.href='$url';\">
		Edit
		</button>
		";
	}
	$type = View::escape($topic->getTopicType());
	$number = View::escape($topic->getNumber());
	$date = $topic->getDate('n/j/Y');
	$description = View::escape($topic->getDescription());
	$synopsis = View::escape($topic->getSynopsis());
	echo "
	<div class=\"topic\">
		<h3>$editButton
			<a href=\"{$topic->getURL()}\">$number</a>
			<span style=\"font-size:.8em;\">$date</span>
		</h3>
		<p>$description</p>
	";

	$votingRecords = array();
	if ($topic->hasVotes()) {
		foreach ($topic->getLatestVotingRecords() as $vote) {
			$name = View::escape($vote->getPerson()->getLastname());
			$votingRecords[$vote->getPosition()][] = "<a href=\"{$vote->getPerson()->getURL()}\">$name</a>";
		}
		foreach ($votingRecords as $position=>$names) {
			$votingRecords[$position] = implode(', ',$names);
		}
		if ($votingRecords) {
			echo "<div>";
			if (isset($votingRecords['yes'])) {
				echo "yes($votingRecords[yes]); ";
			}
			if (isset($votingRecords['no'])) {
				echo "no($votingRecords[no]); ";
			}
			if (isset($votingRecords['abstain'])) {
				echo "abstain($votingRecords[abstain]); ";
			}
			if (isset($votingRecords['absent'])) {
				echo "absent($votingRecords[absent]); ";
			}
			echo "</div>";
		}
	}
	echo "</div>";
}
