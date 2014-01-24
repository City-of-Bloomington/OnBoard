<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Zend\Db\ResultSet $this->topics
 * @param array $this->people An Array of Person objects
 */
use Blossom\Classes\View;

$topic_ids = [];
foreach ($this->topics as $topic) {
	$topic_ids[] = $topic->getId();
}

$xAxisPeople = $this->people;
$yAxisPeople = $this->people;
if (count($xAxisPeople)) {
	echo "
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
					$percent = $yPerson->getAccordancePercentage($xPerson, $topic_ids);
					if ($percent) { $percent.= '%'; }
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
	";
}
