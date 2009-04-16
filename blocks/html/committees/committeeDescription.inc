<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Committee $this->committee
 */
$name = View::escape($this->committee->getName());
$description = View::escape($this->committee->getDescription());

echo "
<h1><a href=\"{$this->committee->getURL()}\">$name</a></h1>
<p>$description</p>
";
