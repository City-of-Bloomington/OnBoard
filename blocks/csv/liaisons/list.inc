<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Zend\Db\Result $this->data
 */
use Web\Url;

$filename = APPLICATION_NAME.'-Liaisons-'.date('Ymd');
header("Content-Disposition: attachment; filename=\"$filename.csv\"");

$columns = [];
foreach ($this->data['fields'] as $f) {
    $columns[] = $this->_($f);
}
echo implode(',', $columns)."\n";

$out = fopen('php://output', 'w');
foreach ($this->data['results'] as $row) {
    fputcsv($out, $row);
}
fclose($out);
