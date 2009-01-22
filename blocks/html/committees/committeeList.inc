<?php
/**
 * @copyright 2006-2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @param CommitteeList $this->committeeList
 */
?>
<div class="interfaceBox">
	<h1>
		<?php
			$userIsAdmin = userHasRole(array('Administrator','Clerk'));
			if ($userIsAdmin)
			{
				$url = new URL(BASE_URL.'/committees/addCommittee.php');
				echo "<a class=\"add button\" href=\"$url\">Add</a>";
			}
		?>
		Committees
	</h1>
	<table>
	<tr><th></th>
		<th>Name</th>
		<th>Seats</th>
	</tr>
	<?php
		foreach ($this->committeeList as $committee)
		{
			$name = View::escape($committee->getName());
			$seats = $committee->getMaxCurrentMembers();

			$editButton = '';
			if ($userIsAdmin)
			{
				$url = new URL(BASE_URL.'/committees/updateCommittee.php');
				$url->committee_id = $committee->getId();
				$editButton = "<a class=\"edit button\" href=\"$url\">Edit</a>";
			}

			$url = new URL(BASE_URL.'/committees/viewCommittee.php');
			$url->committee_id = $committee->getId();
			echo "
			<tr><td>$editButton</td>
				<td><a href=\"$url\">$name</a></td>
				<td>$seats</td>
			</tr>
			";
		}
	?>
	</table>
</div>
