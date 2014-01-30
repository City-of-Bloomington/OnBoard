<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Committee $this->committee
 */
$c = [
	'name'             => $this->committee->getName(),
	'description'      => $this->committee->getDescription(),
	'statutoryName'    => $this->committee->getStatutoryName(),
	'statuteReference' => $this->committee->getStatuteReference(),
	'description'      => $this->committee->getDescription(),
];
echo json_encode($c, JSON_PRETTY_PRINT);
