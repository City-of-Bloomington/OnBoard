<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Application\Models\Media;

include '../../configuration.inc';

copy(__DIR__.'/testfile', SITE_HOME.'/testfile');

Media::convertToPDF(SITE_HOME.'/testfile');