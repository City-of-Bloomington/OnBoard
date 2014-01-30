<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Committee $this->committee
 */
$seats = [];

foreach ($this->committee->getCurrentSeats() as $seat) {
	$members = [];

	foreach ($seat->getCurrentTerms() as $term) {
		$offices = [];
		foreach ($term->getPerson()->getOffices($seat->getCommittee(), date('Y-m-d')) as $office) {
			$offices[] = $office->getTitle();
		}
		$members[] = [
			'name'     =>$term->getPerson()->getFullname(),
			'termStart'=>$term->getTerm_start(DATE_FORMAT),
			'termEnd'  =>$term->getTerm_end(DATE_FORMAT),
			'offices'  =>implode($offices)
		];
	}

	$seats[] = [
		'name'           =>$seat->getName(),
		'appointedBy'    =>$seat->getAppointer()->getName(),
		'maxCurrentTerms'=>$seat->getMaxCurrentTerms(),
		'currentMembers' =>$members
	];
}
echo json_encode($seats, JSON_NUMERIC_CHECK|JSON_PRETTY_PRINT);
