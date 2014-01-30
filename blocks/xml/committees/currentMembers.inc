<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Committee $this->committee
 */
use Blossom\Classes\View;

echo "<seats>";
foreach ($this->committee->getSeats() as $seat) {
	$name      = View::escape($seat->getName());
	$appointer = View::escape($seat->getAppointer());
	echo "
	<seat name=\"$name\">
		<appointedBy>$appointer</appointedBy>
		<currentMembers>
	";
		//--------------------------------------------------------
		// Show all the current terms
		//--------------------------------------------------------
		$terms = array();
		foreach ($seat->getCurrentTerms() as $term) {
			$offices = array();
			foreach ($term->getPerson()->getOffices($seat->getCommittee(), date('Y-m-d')) as $office) {
				$offices[] = View::escape($office->getTitle());
			}
			$offices = implode(',',$offices);

			$fullname = View::escape($term->getPerson()->getFullname());
			echo "
			<member>
				<name>$fullname</name>
				<term>{$term->getTerm_start(DATE_FORMAT)} - {$term->getTerm_end(DATE_FORMAT)}</term>
				<offices>$offices</offices>
			</member>
			";
		}
	echo "
		</currentMembers>
	</seat>
	";
}
echo "</seats>";
