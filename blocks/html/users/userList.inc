<?php
/**
 * @copyright Copyright (C) 2007-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param UserList $this->userList
 */
$addButton = '';
if (userHasRole('Administrator'))
{
	$url = new URL(BASE_URL.'/users/addUser.php');
	$url->return_url = $_SERVER['REQUEST_URI'];
	$addButton = "<a class=\"add button\" href=\"$url\">Add</a>";
}
?>
<div class="interfaceBox">
	<h1><?php echo $addButton; ?>Users</h1>
	<table>
	<?php
		foreach($this->userList as $user)
		{
			$editButton = '';
			$deleteButton = '';
			if (userHasRole('Administrator'))
			{
				$url = new URL(BASE_URL.'/users/updateUser.php');
				$url->user_id = $user->getId();
				$url->return_url = $_SERVER['REQUEST_URI'];
				$editButton = "<a class=\"edit button\" href=\"$url\">Edit</a>";

				$url = new URL(BASE_URL.'/users/deleteUser.php');
				$url->user_id = $user->getId();
				$deleteButton = "
				<button type=\"button\"
						class=\"delete\"
						onclick=\"FRAMEWORK.deleteConfirmation('$url');\">
					Delete
				</button>
				";
			}

			$firstname = View::escape($user->getFirstname());
			$lastname = View::escape($user->getLastname());
			$roles = implode(' ',$user->getRoles());
			echo "
			<tr><td>$editButton
					$deleteButton
				</td>
				<td>{$user->getUsername()}</td>
				<td>$firstname $lastname</td>
				<td>{$user->getAuthenticationMethod()}</td>
				<td>$roles</td>
			</tr>
			";
		}
	?>
	</table>
</div>
