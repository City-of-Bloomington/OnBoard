<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Zend\Db\Result $this->data
 */
$filename = APPLICATION_NAME.'-';
$filename.= $this->title ? $this->title : $this->_(['committee', 'committees', count($this->data)]);
$filename.= '-'.date('Ymd');

header("Content-Disposition: attachment; filename=\"$filename.csv\"");

$fields = [
    'id','type','name','website',
    'email','phone','address','city','state','zip',
    'statutoryName','yearFormed', 'endDate',
    'seats', 'vacancies'
];

$labels = [];
foreach ($fields as $f) { $labels[] = $this->_($f); }
echo implode(',', $labels)."\n";

$out = fopen('php://output', 'w');
foreach ($this->data as $c) {
    $row = [];
    foreach ($fields as $f) {
        $row[] = $c[$f];
    }
    fputcsv($out, $row);
}
fclose($out);
