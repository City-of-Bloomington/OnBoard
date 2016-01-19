<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Person $this->person
 */
$committees = [];
foreach ($this->person->getCommittees() as $committee) {
	$uri = BASE_URI.'/committees/members?committee_id='.$committee->getId();
	$name = "<a href=\"$uri\">".self::escape($committee->getName())."</a>";

	$appointers = [];
	foreach ($this->person->getAppointers($committee) as $appointer) {
		$appointers[] = self::escape($appointer);
	}
	$appointers = implode(', ',$appointers);


	$offices = $this->person->getOffices($committee);
	if (count($offices)) {
		$o = $offices->current();
		$office = self::escape($o->getTitle());
		$office = "($office)";
	}
	else {
		$office = '';
	}
	$current = $this->person->isCurrentlyServing($committee)
		? '('.$this->_(['current_member', 'current_members', 1]).')'
		: '';
	$committees[] = "<li>$name-$appointers $office $current</li>";
}

if ($committees) {
	echo "<ul>";
	foreach ($committees as $committee) {
		echo $committee;
	}
	echo "</ul>";
}
