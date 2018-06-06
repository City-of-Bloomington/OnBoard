<?php
/**
 * @copyright 2016-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Committee $this->committee
 */
$count     = $this->committee->getVacancyCount();
$vacancies = $count ? "($count {$this->_(['vacancy', 'vacancies', $count])})" : '';
$name      = parent::escape($this->committee->getName());
echo "<h1>$name $vacancies</h1>";
