<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
include '../bootstrap.inc';

// Cause an error and see what happens.
$committee = new Committee('CCCC');
echo "\n";
