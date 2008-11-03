<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @param MemberList $this->memberList
 * @param Seat $this->seat (optional)
 */
$addButton = '';
if (isset($this->seat) && userHasRole(array('Administrator','Clerk')))
{
	$url = new URL(BASE_URL.'/members/addMember.php');
	$url->seat_id = $this->seat->getId();
	$addButton = "<a class=\"add button\" href=\"$url\">Add</a>";
}
?>
<div class="interfaceBox">
	<h1><?php echo $addButton; ?>
		Members
	</h1>
	<table>
	<?php
		foreach($this->memberList as $member)
		{
			$editButton = '';
			if (userHasRole(array('Administrator','Clerk')))
			{
				$url = new URL(BASE_URL.'/members/updateMember.php');
				$url->member_id = $member->getId();
				$editButton = "<a class=\"edit button\" href=\"$url\">Edit</a>";
			}

			$deleteButton = '';
			if (userHasRole(array('Administrator','Clerk')))
			{
				$url = new URL(BASE_URL.'/members/deleteMember.php');
				$url->member_id = $member->getId();
				$deleteButton = "
				<button type=\"button\"
						class=\"delete\"
						onclick=\"FRAMEWORK.deleteConfirmation('$url');\">
					Delete
				</button>
				";
			}

			$name = View::escape("{$member->getFirstname()} {$member->getLastname()}");
			echo "
			<tr><td>$editButton $deleteButton</td>
				<td><a href=\"{$member->getURL()}\">$name</a></td>
				<td>{$member->getTerm_start('n/j/Y')} - {$member->getTerm_end('n/j/Y')}</td>
			</tr>
			";
		}
	?>
	</table>
</div>
