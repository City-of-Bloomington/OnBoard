<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param array $this->types An arra of Legislation Type objects
 */
declare (strict_types=1);
$output = [];
foreach ($this->types as $t) {
    $output[] = [
        'id'      => (int)$t->getId(),
        'name'    =>      $t->getName(),
        'subtype' =>      $t->isSubtype()
    ];
}
echo json_encode($output, JSON_PRETTY_PRINT);
