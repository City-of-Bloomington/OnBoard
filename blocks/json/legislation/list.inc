<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param array       $this->list   An array of Legislation objects
 * @param Legislation $this->parent The parent legislation for the list
 */
$output = [];

foreach ($this->list as $l) {
    $output[] = $l->toArray();
}
echo json_encode($output, JSON_PRETTY_PRINT);
