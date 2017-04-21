<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 *
 * 	public static $types = ['Agenda', 'Minutes', 'Packet'];
 */
use Application\Models\Committee;
use Application\Models\MeetingFile;
include '../../bootstrap.inc';

$committee = new Committee(10);
$type      = 'Agenda';

#define('SKIP_DB_VALIDATION', MeetingFile::VALIDATION_ALL & ~MeetingFile::VALIDATION_DB);

$FILES = fopen('./agendas.csv', 'r');
while (($line = fgets($FILES)) !== false) {
    list($file, $date) = explode('|', $line);

    $meetingFile = new MeetingFile();
    $meetingFile->setCommittee($committee);
    $meetingFile->setType($type);
    $meetingFile->setMeetingDate($date);
    $meetingFile->setFile($file);
    #$meetingFile->validation = SKIP_DB_VALIDATION;
    $meetingFile->save();
    echo "{$meetingFile->getMeetingDate('Y-m-d')} {$meetingFile->getFilename()}\n";
}
