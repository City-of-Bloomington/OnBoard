<?php
/**
 * Template for having the browser download a file
 *
 * @copyright 2013-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
header('Expires: 0');
header('Pragma: cache');
header('Cache-Control: private');
echo $this->includeBlocks();
