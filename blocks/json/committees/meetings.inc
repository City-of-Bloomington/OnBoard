<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Committee $htis->committee
 * @param array     $this->meetings
 * @param int       $this->year
 */
use Application\Models\MeetingFile;

use Blossom\Classes\Block;
use Blossom\Classes\Url;


$out = [];
foreach ($this->meetings as $date=>$meeting) {

    $out[$date]['eventId'] = !empty($meeting['eventId']) ? $meeting['eventId'] : '';

    foreach (MeetingFile::$types as $type) {
        if (!empty(  $meeting['files'][$type])) {
            foreach ($meeting['files'][$type] as  $file) {
                  $out[$date]['files'][$type][] = $file->getData();
            }
        }
    }
}
echo json_encode($out, JSON_PRETTY_PRINT);
