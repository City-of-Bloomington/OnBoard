<?php
/**
 * @copyright 2016-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Application\Models\File;

include '../../bootstrap.inc';

copy(__DIR__.'/testfile', SITE_HOME.'/testfile');

File::convertToPDF(SITE_HOME.'/testfile');
