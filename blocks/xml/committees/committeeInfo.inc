<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Committee $this->committee
 */
$name = View::escape($this->committee->getName());
$description = View::escape($this->committee->getDescription());
$statutoryName = View::escape($this->committee->getStatutoryName());
$statuteReference = View::escape($this->committee->getStatuteReference());
$description = View::escape($this->committee->getDescription());

echo "
<committee>
	<url>{$this->committee->getURL()}</url>
	<name>$name</name>
	<dateFormed>{$this->committee->getDateFormed('n/j/Y')}</dateFormed>
	<statutoryName>$statutoryName</statutoryName>
	<statuteReference>$statuteReference</statuteReference>
	<description>$description</description>
";
