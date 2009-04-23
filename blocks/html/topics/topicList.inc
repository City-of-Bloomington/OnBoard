<?php
/**
 * @copyright 2006-2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param TopicList $this->topicList
 * @param Committee $this->committee (optional)
 * @param Tag $this->tag
 */
$userCanEdit = userHasRole(array('Administrator','Clerk'));

$addTopicButton = '';
if (isset($this->committee) && $userCanEdit) {
	$url = new URL(BASE_URL.'/topics/addTopic.php');
	$url->committee_id = $this->committee->getId();
	$addTopicButton = "<a class=\"add button\" href=\"$url\">Add</a>";
}

if (count($this->topicList) || $userCanEdit) {
	$title = isset($this->tag) ? View::escape($this->tag->getName()) : '';

	$numberSortURL = new URL($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
	if ($this->topicList->getSort()=='topics.number') {
		$numberSortURL->sort = 'number desc';
		$numberDirection = 'down';
	}
	else {
		$numberSortURL->sort = 'number';
		$numberDirection = 'up';
	}

	$dateSortURL = new URL($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
	if ($this->topicList->getSort()=='topics.date') {
		$dateSortURL->sort = 'date desc';
		$dateDirection = 'down';
	}
	else {
		$dateSortURL->sort = 'date';
		$dateDirection = 'up';
	}

	echo "
	<div class=\"interfaceBox legislation\">
		<h1>$addTopicButton $title Legislation</h1>
		<div>
			<label>Sort:</label>
			<a href=\"$numberSortURL\" class=\"$numberDirection\">Number</a>
			<a href=\"$dateSortURL\" class=\"$dateDirection\">Date</a>
		</div>
	";
		foreach ($this->topicList as $topic) {
			$editButton = '';
			if ($userCanEdit) {
				$url = new URL(BASE_URL.'/topics/updateTopic.php');
				$url->topic_id = $topic->getId();
				$editButton = "<a class=\"edit button\" href=\"$url\">Edit</a>";
			}
			$type = View::escape($topic->getTopicType());
			$number = View::escape($topic->getNumber());
			$date = $topic->getDate('n/j/Y');
			$description = View::escape($topic->getDescription());
			$synopsis = View::escape($topic->getSynopsis());
			echo "
			<div class=\"topic\">
				<h3>$editButton <a href=\"{$topic->getURL()}\">$number</a> <span style=\"font-size:.8em;\"> $date</span></h3>
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
	echo "
	</div>
	";
}
