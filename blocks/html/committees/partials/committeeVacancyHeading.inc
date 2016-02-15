<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
$c = $this->committee->getVacancyCount();
$vacancies = $c ? "($c {$this->_(['vacancy', 'vacancies', $c])})" : '';
$name = self::escape($this->committee->getName());
echo "<h1>$name $vacancies</h1>";