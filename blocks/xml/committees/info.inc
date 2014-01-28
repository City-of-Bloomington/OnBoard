<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Committee $this->committee
 */
use Blossom\Classes\View;

$name             = View::escape($this->committee->getName());
$description      = View::escape($this->committee->getDescription());
$statutoryName    = View::escape($this->committee->getStatutoryName());
$statuteReference = View::escape($this->committee->getStatuteReference());
$description      = View::escape($this->committee->getDescription());

echo "
<committee>
	<url>{$this->committee->getUrl()}</url>
	<name>$name</name>
	<dateFormed>{$this->committee->getDateFormed(DATE_FORMAT)}</dateFormed>
	<statutoryName>$statutoryName</statutoryName>
	<statuteReference>$statuteReference</statuteReference>
	<description>$description</description>
";
