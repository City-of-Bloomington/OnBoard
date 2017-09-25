<?php
/**
 * @copyright 2016-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Application\Models\File;

$_SERVER['SITE_HOME'] = __DIR__;

include '../../bootstrap.inc';

copy(__DIR__.'/testfile', SITE_HOME.'/test');

File::convertToPDF(SITE_HOME.'/test');
