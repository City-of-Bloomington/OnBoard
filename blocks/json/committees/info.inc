<?php
/**
 * @copyright 2009-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/agpl.html GNU/AGPL, see LICENSE.txt
 * @param Committee $this->committee
 */
echo json_encode($this->committee->toArray(), JSON_PRETTY_PRINT);
