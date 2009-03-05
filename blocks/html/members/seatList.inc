<?php
/**
 * @copyright 2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param MemberList $this->memberList
 */
?>
<div class="interfaceBox memberSeat">
	<h1>Seats</h1>
	<table>
	<tr><th>Committee</th>
		<th>Title</th>
		<th>Term</th>
	</tr>
	<?php
		foreach ($this->memberList as $member)
		{
			$committee = View::escape($member->getCommittee()->getName());
			$title = View::escape($member->getSeat()->getTitle());
			$term = "{$member->getTerm_start('n/j/Y')} - {$member->getTerm_end('n/j/Y')}";
			echo "
			<tr><td><a href=\"{$member->getCommittee()->getURL()}\">$committee</a></td>
				<td><a href=\"{$member->getSeat()->getURL()}\">$title</a></td>
				<td>$term</td>
			</tr>
			";
		}
	?>
	</table>
</div>
