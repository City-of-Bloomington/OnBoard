<?php
/**
 * @copyright 2006-2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @param CommitteeList $this->committeeList
 */
?>
<committees>
<?php
	foreach ($this->committeeList as $committee)
	{
		echo "\t<committee id=\"{$committee->getId()}\">\n";

		$name = View::escape($committee->getName());
		$statutoryName = View::escape($committee->getStatutoryName());
		$reference = View::escape($committee->getStatuteReference());
		$seats = $committee->getMaxCurrentMembers();
		$description = View::escape($committee->getDescription());
		echo "
			<name>$name</name>
			<statutoryName>$statutoryName</statutoryName>
			<statuteReference>$reference</statuteReference>
			<numberOfSeats>$seats</numberOfSeats>
			<dateFormed>{$committee->getDateFormed('Y-m-d')}</dateFormed>
			<url>{$committee->getWebsite()}</url>
		";

		echo "\t</committee>\n";
	}
?>
</committeees>
