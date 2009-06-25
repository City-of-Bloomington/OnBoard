<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Committee $this->committee
 */
echo "<seats>";
foreach ($this->committee->getSeats() as $seat) {
	$name = View::escape($seat->getName());
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
		$c = 0;
		foreach ($seat->getCurrentTerms() as $term) {
			$c++;


			$offices = array();
			foreach ($term->getPerson()->getOffices($seat->getCommittee(),time()) as $office) {
				$offices[] = View::escape($office->getTitle());
			}
			$offices = implode(',',$offices);

			$fullname = View::escape($term->getPerson()->getFullname());
			echo "
			<member>
				<name>$fullname</name>
				<term>{$term->getTerm_start('n/j/Y')} - {$term->getTerm_end('n/j/y')}</term>
				<offices>$offices</offices>
			</member>
			";
		}
	echo "
		</currentMembers>
	</seat>
	";
}
echo "
</seats>
</committee>
";