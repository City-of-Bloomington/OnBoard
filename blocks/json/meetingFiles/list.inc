<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Laminas\Db\Result $this->files
 * @param Committee      $this->committee (optional)
 * @param stdClass       $this->sort
 */
$meetingFiles = [];
foreach ($this->files as $f) {
    $meetingFiles[] = $f->getData();
}
$output = [
    'files' => $meetingFiles,
    'pagination' => [
        'current_page' => $this->files->getCurrentPageNumber(),
        'total_pages'  => count($this->files)
    ]
];
echo json_encode($output, JSON_PRETTY_PRINT);
