<?php
/**
 * Outputs a file to the browser
 *
 * @copyright 2013-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Media  $this->media
 */
$mime             = $this->media->getMime_type();
$filename         = $this->media->getFilename();

header("Content-type: $mime");
header("Content-Disposition: attachment; filename=$filename");

readfile($this->media->getFullpath());
