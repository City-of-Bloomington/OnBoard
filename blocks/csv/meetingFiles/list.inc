<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Zend\Db\Result $this->files
 * @param Committee      $this->committee (optional)
 * @param stdClass       $this->sort
 */
$filename = APPLICATION_NAME.'-';
$filename.= $this->title ? $this->title : $this->_(['meetingFile', 'meetingFiles', count($this->files)]);
$filename.= '-'.date('Ymd');
header("Content-Disposition: attachment; filename=\"$filename.csv\"");

$sentColumnNames = false;

$out = fopen('php://output', 'w');
foreach ($this->files as $f) {
    $row = $f->getData();
    if (!$sentColumnNames) {
        echo implode(',', array_keys($row))."\n";
        $sentColumnNames = true;
    }
    fputcsv($out, $row);
}
fclose($out);
