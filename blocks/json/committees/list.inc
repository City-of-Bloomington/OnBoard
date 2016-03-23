<?php
/**
 * @copyright 2014-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Zend\Db\ResultSet $this->data
 */
$fields = [
    'id','type','name','website',
    'email','phone','address','city','state','zip',
    'statutoryName','yearFormed',
    'seats', 'vacancies'
];
$data = [];
foreach ($this->data as $c) {
    $row = [];
    foreach ($fields as $f) {
        $row[$f] = $c[$f];
    }
    $data[] = $row;
}
echo json_encode($data);
