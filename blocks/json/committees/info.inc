<?php
/**
 * @copyright 2009-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @param Committee $this->committee
 */
use Application\Models\LiaisonTable;
use Michelf\MarkdownExtra;

$markdown = new MarkdownExtra();
$description     = $markdown->transform($this->committee->getDescription());
$meetingSchedule = $markdown->transform($this->committee->getMeetingSchedule());

$c = [
    'id'               => $this->committee->getId(),
    'type'             => $this->committee->getType(),
	'name'             => $this->committee->getName(),
	'statutoryName'    => $this->committee->getStatutoryName(),
	'website'          => $this->committee->getWebsite(),
	'videoArchive'     => $this->committee->getVideoArchive(),
	'email'            => $this->committee->getEmail(),
	'phone'            => $this->committee->getPhone(),
    'address'          => $this->committee->getAddress(),
    'city'             => $this->committee->getCity(),
    'state'            => $this->committee->getState(),
    'zip'              => $this->committee->getZip(),
	'meetingSchedule'  => $meetingSchedule,
	'vacancy'          => $this->committee->hasVacancy(),
    'description'      => $description,
];
$statutes = $this->committee->getStatutes();
if (count($statutes)) {
    $c['statutes'] = [];
    foreach ($statutes as $s) {
        $c['statutes'][] = ['citation'=>$s->getCitation(), 'url'=>$s->getUrl()];
    }
}

$c['liaisons'] = [];
$data = LiaisonTable::committeeLiaisonData(['committee_id'=>$this->committee->getId()]);
foreach ($data['results'] as $row) {
    $liaison = [];
    foreach ($row as $f=>$v) { $liaison[$f] = $v; }

    $c['liaisons'][] = $liaison;
}

echo json_encode($c, JSON_PRETTY_PRINT);
