<?php
/**
 * Outputs a file to the browser
 *
 * @copyright 2013-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Application\Models\File  $this->downloadFile
 */
$mime             = $this->downloadFile->getMime_type();
$filename         = $this->downloadFile->getFilename();

header("Content-type: $mime");
header("Content-Disposition: attachment; filename=$filename");

readfile($this->downloadFile->getFullpath());
