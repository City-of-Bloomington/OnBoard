<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Person $this->person
 */
$about = nl2br(View::escape($this->person->getAbout()));
echo "
<div id=\"personalInfo\">
	<p class=\"memberDescription\">
	<table class=\"memberTable\">
	<tbody>
";
	if ($this->person->getEmail()) {
		echo "<tr><th>Email</th><td>{$this->person->getEmail()}</td></tr>";
	}
	if ($this->person->getAddress()) {
		echo "<tr><th>Address</th><td>{$this->person->getAddress()}</td></tr>";
	}
	if ($this->person->getBirthdate()) {
		echo "<tr><th>Birthdate</th><td>{$this->person->getBirthdate('n/j/Y')}</td></tr>";
	}

	$committees = array();
	foreach ($this->person->getCommittees() as $committee) {
		$name = View::escape($committee->getName());
		$name = "<a href=\"{$committee->getURL()}\">$name</a>";

		$appointers = array();
		foreach ($this->person->getAppointers($committee) as $appointer) {
			$appointers[] = View::escape($appointer);
		}
		$appointers = implode(', ',$appointers);


		$offices = $this->person->getOffices($committee);
		if (count($offices)) {
			$office = View::escape($offices[0]->getTitle());
			$office = "($office)";
		}
		else {
			$office = '';
		}
		$current = $this->person->isCurrentlyServing($committee) ? '(Current Member)' : '';
		$committees[] = "<li>$name-$appointers $office $current</li>";
	}
	if ($committees) {
		echo "
		<tr><th>Committees</th>
			<td><ul>
		";
		foreach ($committees as $committee) {
			echo $committee;
		}
		echo "
				</ul>
			</td>
		</tr>
		";
	}

echo "
	</tbody>
	</table>
	$about</p>
</div>
";

$this->termList = $this->person->getTerms();
if (count($this->termList)) {
	include APPLICATION_HOME.'/blocks/html/terms/termList.inc';
}
