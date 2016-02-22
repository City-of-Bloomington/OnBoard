<?php
/**
 * @copyright 2014-2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Zend\Db\ResultSet $this->data
 */
$fields = [
    'id','type','name','website',
    'email','phone','address','city','state','zip',
    'statutoryName','statuteReference','statuteUrl','yearFormed',
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
