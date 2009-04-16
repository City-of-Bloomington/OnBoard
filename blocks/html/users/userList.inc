<?php
/**
 * @copyright 2007-2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param UserList $this->userList
 */
?>
<div class="interfaceBox">
	<h1><?php
			if (userHasRole('Administrator')) {
				echo "
				<a class=\"add button\" href=\"".BASE_URL."/users/addUser.php\" title=\"Add user account\">
					Add
				</a>
				";
			}
		?>
		User Accounts
	</h1>
	<table>
	<?php
		foreach ($this->userList as $user) {
			$editButton = '';
			$deleteButton = '';
			if (userHasRole('Administrator')) {
				$url = new URL(BASE_URL.'/users/updateUser.php');
				$url->user_id = $user->getId();
				$editButton = "
				<a class=\"edit button\" href=\"$url\" title=\"Edit user account\">
					Edit
				</a>
				";

				$url = BASE_URL.'/users/deleteUser.php?user_id='.$user->getId();
				$deleteButton = "
				<a class=\"delete button\" href=\"$url\" title=\"Delete user account\">
					Delete
				</a>
				";
			}

			echo "
			<tr><td>$editButton</td>
				<td>{$user->getUsername()}</td>
				<td>{$user->getFirstname()} {$user->getLastname()}</td>
				<td>{$user->getAuthenticationMethod()}</td>
				<td>
			";
					foreach ($user->getRoles() as $role) {
						echo "$role ";
					}
			echo "
				</td>
			</tr>
			";
		}
	?>
	</table>
</div>
