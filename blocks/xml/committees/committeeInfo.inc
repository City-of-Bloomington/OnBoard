<?php
/**
 * @copyright 2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Committee $this->committee
 */
$name = View::escape($this->committee->getName());
$statutoryName = View::escape($this->committee->getStatutoryName());
$reference = View::escape($this->committee->getStatuteReference());
$seats = $this->committee->getMaxCurrentMembers();
$description = View::escape($this->committee->getDescription());
echo "
<committee id=\"{$this->committee->getId()}\">
	<name>$name</name>
	<statutoryName>$statutoryName</statutoryName>
	<statuteReference>$reference</statuteReference>
	<numberOfSeats>$seats</numberOfSeats>
	<dateFormed>{$this->committee->getDateFormed('Y-m-d')}</dateFormed>
	<url>{$this->committee->getWebsite()}</url>
";

include APPLICATION_HOME.'/blocks/xml/committees/seats.inc';

$this->topicList = $this->committee->getTopics();
include APPLICATION_HOME.'/blocks/xml/topics/topicList.inc';

echo "</committee>";
