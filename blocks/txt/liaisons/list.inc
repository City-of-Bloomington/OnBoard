<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Zend\Db\Result $this->data
 */
foreach ($this->data['results'] as $row) {
    echo "$row[firstname] $row[lastname] <$row[email]>,\n";
}
