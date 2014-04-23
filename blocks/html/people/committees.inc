<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Person $this->person
 */
use Blossom\Classes\View;

$committees = [];
foreach ($this->person->getCommittees() as $committee) {
	$name = View::escape($committee->getName());
	$name = "<a href=\"{$committee->getUri()}\">$name</a>";

	$appointers = array();
	foreach ($this->person->getAppointers($committee) as $appointer) {
		$appointers[] = View::escape($appointer);
	}
	$appointers = implode(', ',$appointers);


	$offices = $this->person->getOffices($committee);
	if (count($offices)) {
		$o = $offices->current();
		$office = View::escape($o->getTitle());
		$office = "($office)";
	}
	else {
		$office = '';
	}
	$current = $this->person->isCurrentlyServing($committee) ? '(Current Member)' : '';
	$committees[] = "<li>$name-$appointers $office $current</li>";
}

if ($committees) {
	echo "<ul>";
	foreach ($committees as $committee) {
		echo $committee;
	}
	echo "</ul>";
}
