<?php
/**
 * @copyright 2009-2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Committee $this->committee
 */
use Michelf\MarkdownExtra;

$markdown = new MarkdownExtra();
$description     = $markdown->transform($this->committee->getDescription());
$contactInfo     = $markdown->transform($this->committee->getContactInfo());
$meetingSchedule = $markdown->transform($this->committee->getMeetingSchedule());

$c = [
	'name'             => $this->committee->getName(),
	'statutoryName'    => $this->committee->getStatutoryName(),
	'statuteReference' => $this->committee->getStatuteReference(),
	'statuteUrl'       => $this->committee->getStatuteUrl(),
	'website'          => $this->committee->getWebsite(),
	'email'            => $this->committee->getEmail(),
	'phone'            => $this->committee->getPhone(),
    'address'          => $this->committee->getAddress(),
    'city'             => $this->committee->getCity(),
    'state'            => $this->committee->getState(),
    'zip'              => $this->committee->getZip(),
	'contactInfo'      => $contactInfo,
	'meetingSchedule'  => $meetingSchedule,
	'vacancy'          => $this->committee->hasVacancy(),
    'description'      => $description,
];

echo json_encode($c, JSON_PRETTY_PRINT);
