<?php
/**
 * @copyright 2006-2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @param Committee $this->committee
 */
?>
<seats>
<?php
	foreach ($this->committee->getSeats() as $seat)
	{
		$title = View::escape($seat->getTitle());
		$appointer = View::escape($seat->getAppointer());
		echo "
		<seat id=\"{$seat->getId()}\">
			<title>$title</title>
			<appointer>$appointer</appointer>
			<maxCurrentMembers>{$seat->getMaxCurrentMembers()}</maxCurrentMembers>
			<members>
		";
			//--------------------------------------------------------
			// Show all the members
			//--------------------------------------------------------
			$members = array();
			$c = 0;
			foreach ($seat->getCurrentMembers() as $member)
			{
				$c++;
				$fullname = View::escape($member->getFullname());
				echo "
				<member id=\"{$member->getId()}\">
					<name>$fullname</name>
					<term_start>{$member->getTerm_start('Y-m-d')}</term_start>
					<term_end>{$member->getTerm_end('Y-m-d')}</term_end>
				</member>
				";
			}
		echo "
			</members>
		</seat>
		";
	}
?>
</seats>
