<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Person $this->person
 */
use Blossom\Classes\View;

if ($this->person->hasVotingRecord()) {
	echo "
	<div class=\"votingRecord\">
		<h2>{$this->_(['labels.votingRecord', 'labels.votingRecords', 1])}</h2>
	";

	foreach ($this->person->getVotingRecords() as $record) {
		$position = View::escape($record->getPosition());
		switch ($position) {
			case 'yes':
			case 'no':
				$action = 'voted '.ucfirst($position);
				break;
			case 'absent':
				$action = "was Absent";
				break;
			case 'abstain':
				$action = "Abstained";
				break;
		}
		$number      = View::escape($record->getTopic()->getNumber());
		$description = View::escape($record->getTopic()->getDescription());
		$type        = View::escape($record->getVoteType());
		$lastname    = View::escape($this->person->getLastname());
		$date = $record->getDate(DATE_FORMAT);
		echo "
		<div class=\"topic $position\">
			<h3>$lastname $action in a $type vote on
				<a href=\"{$record->getTopic()->getUri()}\" class=\"number\">$number</a>
				<span class=\"date\">$date</span>
			</h3>
			<p>$description</p>
		";



		$votingRecords = array();
		foreach ($record->getVote()->getVotingRecords() as $vote) {
			$name = View::escape($vote->getPerson()->getLastname());
			$class = ($vote->getPerson()->getId()==$this->person->getId())
					? 'class="current"'
					: '';
			$votingRecords[$vote->getPosition()][] = "<a href=\"{$vote->getPerson()->getUri()}\" $class>$name</a>";
		}
		foreach ($votingRecords as $position=>$names) {
			$votingRecords[$position] = "$position(".implode(', ',$names).")";
		}
		if ($votingRecords) {
			echo "<div class=\"voteSummary\">";
			echo implode('; ',$votingRecords);
			echo "</div>";
		}
		echo "</div>";
	}
	echo "
	</div>
	";
}
