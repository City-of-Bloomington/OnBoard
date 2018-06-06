<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Legislation $this->legisation
 */
$type   = parent::escape($this->legislation->getType()->getName());
$year   = parent::escape($this->legislation->getYear());
$number = parent::escape($this->legislation->getNumber());
$title  = parent::escape($this->legislation->getTitle());
echo "
<h1>$year $type $number</h1>
<h2>$title</h2>
";
