<?php
/**
 * @copyright 2017-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
include '../Web/bootstrap.php';

// Cause an error and see what happens.
$committee = new Committee('CCCC');
echo "\n";
