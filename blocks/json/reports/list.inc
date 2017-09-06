<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param array     $this->list       An array of Report objects
 * @param Committee $this->committee
 */
declare (strict_types=1);

$output = [];

foreach ($this->list as $r) {
    $output[] = $r->toArray();
}
echo json_encode($output, JSON_PRETTY_PRINT);
