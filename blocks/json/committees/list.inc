<?php
/**
 * @copyright 2014-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Laminas\Db\ResultSet $this->data
 */
$fields = [
    'id','type','name','website', 'videoArchive', 'calendarId',
    'email','phone','address','city','state','zip',
    'statutoryName','yearFormed', 'legislative',
    'seats', 'vacancies'
];
$data = [];
foreach ($this->data as $c) {
    $row = [];
    foreach ($fields as $f) {
        switch ($f) {
            case 'yearFormed':
                $row[$f] = $c[$f] ? (int)$c[$f] : null;
            break;

            case 'id':
            case 'seats':
            case 'vacancies':
                $row[$f] = (int)$c[$f];
            break;

            case 'legislative':
                $row[$f] = $c[$f] ? true : false;
            break;

            default:
                $row[$f] = $c[$f];
        }
    }
    $data[] = $row;
}
echo json_encode($data, JSON_PRETTY_PRINT);
