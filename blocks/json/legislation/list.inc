<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param array       $this->list   An array of Legislation objects
 * @param Legislation $this->parent The parent legislation for the list
 */
use Application\Models\Person;
use Application\Models\Legislation\ActionTypesTable;
use Application\Models\Legislation\TypesTable;

use Blossom\Classes\Url;

$output = [];

foreach ($this->list as $l) {
    $output[] = $l->toArray();
}
echo json_encode($output, JSON_PRETTY_PRINT);
