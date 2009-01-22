<?php
/**
 * @copyright 2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param MemberList $this->memberList
 */
?>
<seats>
<?php
	foreach ($this->memberList as $member)
	{
		$title = View::escape($member->getSeat()->getTitle());
		$term = "{$member->getTerm_start('n/j/Y')} - {$member->getTerm_end('n/j/Y')}";
		$committee = $member->getCommittee();
		$committeeName = View::escape($committee->getName());
		echo "
		<seat id=\"{$member->getSeat()->getId()}\">
			<committee id=\"{$committee->getId()}\">$committeeName</committee>
			<title>$title</title>
			<term_start>{$member->getTerm_start('Y-m-d')}</term_start>
			<term_end>{$member->getTerm_end('Y-m-d')}</term_end>
		</seat>
		";
	}
?>
</seats>
