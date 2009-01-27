<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param TopicList $this->topicList
 */
$xAxisMembers = $this->topicList->getMembers();
$yAxisMembers = $this->topicList->getMembers();
if (count($xAxisMembers)) {
	echo "
	<div class=\"interfaceBox\">
		<h1>Voting Comparison</h1>
		<table>
			<tr><th></th>
	";
		foreach ($xAxisMembers as $xMember) {
			$name = View::escape($xMember->getLastname());
			echo "<th>$name</th>";
		}
		echo "</tr>";

		foreach ($yAxisMembers as $yMember) {
			$name = View::escape($yMember->getLastname());
			echo "
			<tr><th>$name</th>
			";

			foreach ($xAxisMembers as $xMember) {
				if ($xMember->getId() != $yMember->getId()) {
					$percent = $yMember->getAccordancePercentage($xMember,$this->topicList);
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
