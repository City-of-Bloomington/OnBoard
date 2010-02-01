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
				<button type='\button\" class=\"add\" onclick=\"document.location.href='".BASE_URL."/users/addUser.php';\">
					Add User Account
				</button>
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
				<button type=\"button\" class=\"edit\" onclick=\"document.location.href='$url';\">
					Edit
				</button>
				";

				$url = BASE_URL.'/users/deleteUser.php?user_id='.$user->getId();
				$deleteButton = "
				<button type=\"button\" class=\"delete\" onclick=\"document.location.href=$url';\">
					Delete
				</button>
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
