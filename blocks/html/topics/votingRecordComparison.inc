<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param TopicList $this->topicList
 */
$xAxisPeople = $this->topicList->getPeople();
$yAxisPeople = $this->topicList->getPeople();
if (count($xAxisPeople)) {
	echo "
	<div class=\"interfaceBox votingComparison\">
		<h1>Voting Comparison</h1>
		<table>
			<tr><th></th>
	";
		foreach ($xAxisPeople as $xPerson) {
			$name = View::escape($xPerson->getLastname());
			echo "<th>$name</th>";
		}
		echo "</tr>";

		foreach ($yAxisPeople as $yPerson) {
			$name = View::escape($yPerson->getLastname());
			echo "
			<tr><th>$name</th>
			";

			foreach ($xAxisPeople as $xPerson) {
				if ($xPerson->getId() != $yPerson->getId()) {
					$percent = $yPerson->getAccordancePercentage($xPerson,$this->topicList);
					$percent = "$percent%";
				}
				else {
					$percent = '-';
				}

				echo "<td>$percent</td>";
			}
			echo "</tr>";
		}
	echo "
		</table>
	</div>
	";
}
