<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param array     $this->years
 * @param Committee $this->committee (optional)
 */
echo '<ul>';
foreach ($this->years as $year=>$count) {
    echo "<li>$year ($count)</li>";
}
echo '</ul>';
