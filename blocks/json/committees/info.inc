<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Committee $this->committee
 */
$c = [
	'name'             => $this->committee->getName(),
	'statutoryName'    => $this->committee->getStatutoryName(),
	'statuteReference' => $this->committee->getStatuteReference(),
	'statuteUrl'       => $this->committee->getStatuteUrl(),
	'website'          => $this->committee->getWebsite(),
	'synopsis'         => $this->committee->getSynopsis(),
	'contactInfo'      => $this->committee->getContactInfo(),
	'meetingSchedule'  => $this->committee->getMeetingSchedule(),
	'description'      => $this->committee->getDescription(),
];

echo json_encode($c, JSON_PRETTY_PRINT);
