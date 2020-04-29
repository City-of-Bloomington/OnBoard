<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Laminas\Db\Result $this->data
 */
$people = [];
foreach ($this->data['results'] as $row) {
    $people[$row['person_id']] = "$row[firstname] $row[lastname] <$row[email]>";
}
echo implode(",\n", $people);
