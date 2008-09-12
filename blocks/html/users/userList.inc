<?php
/**
 * @copyright Copyright (C) 2007-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param UserList $this->userList
 */
if (userHasRole('Administrator'))
{
	$addButton = '<a class="add button" href="'.BASE_URL.'/users/addUser.php">Add</a>';
}
else { $addButton = ''; }
?>
<div class="interfaceBox">
	<h1><?php echo $addButton; ?>Users</h1>
	<table>
	<?php
		foreach($this->userList as $user)
		{
			if (userHasRole('Administrator'))
			{
				$editButton = "<a class=\"edit button\" href=\"".BASE_URL."/users/updateUser.php?id={$user->getId()}\">Edit</a>";
				$deleteButton = "<button type=\"button\" class=\"delete\" onclick=\"FRAMEWORK.deleteConfirmation('deleteUser.php?id={$user->getId()}');\">Delete</button>";
			}
			else
			{
				$editButton = '';
				$deleteButton = '';
			}
			echo "
			<tr><td>{$editButton}{$deleteButton}</td>
				<td>{$user->getUsername()}</td>
				<td>{$user->getFirstname()} {$user->getLastname()}</td>
				<td>{$user->getAuthenticationMethod()}</td>
				<td>
			";
					foreach($user->getRoles() as $role) { echo "$role "; }
			echo "</td></tr>";
		}
	?>
	</table>
</div>