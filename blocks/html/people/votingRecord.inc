<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Person $this->person
 * @param VoteType $this->voteType
 */
$fields = $this->voteType ? array('voteType'=>$this->voteType) : null;
if ($this->person->hasVotingRecord()) {
	echo "
	<div class=\"interfaceBox memberVR\">
		<h1>Voting Record</h1>
	";

	foreach ($this->person->getVotingRecords($fields) as $record) {
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
		$number = View::escape($record->getTopic()->getNumber());
		$date = $record->getDate('n/j/Y');
		$description = View::escape($record->getTopic()->getDescription());
		$lastname = View::escape($this->person->getLastname());
		$type = View::escape($record->getVoteType());
		echo "
		<div class=\"topic\">
			<h3 class=\"$position\">
				$lastname $action in a $type vote on
				<a href=\"{$record->getTopic()->getURL()}\" class=\"number\">$number</a>
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
			$votingRecords[$vote->getPosition()][] = "<a href=\"{$vote->getPerson()->getURL()}\" $class>$name</a>";
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
	echo "</div>";
}
