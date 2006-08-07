<?php
	verifyUser("Administrator");

	include(GLOBAL_INCLUDES."/xhtmlHeader.inc");
	include(APPLICATION_HOME."/includes/banner.inc");
	include(APPLICATION_HOME."/includes/menubar.inc");
	include(APPLICATION_HOME."/includes/sidebar.inc");
?>
<div id="mainContent">
	<div class="interfaceBox">
		<div class="titleBar">
			<button type="button" class="addSmall" onclick="document.location.href='addUserForm.php';">Add</button>
			Users
		</div>
		<table>
			<tr><th></th><th>Username</th><th>Name</th><th>Auth. Method</th><th>Roles</th></tr>
		<?php
			
			# Find all the users and display their username, first/lastname, authentication method and roles.
			$userList = new UserList();
			$userList->find();
			foreach($userList as $user)
			{
				echo "
				<tr><td><button type=\"button\" class=\"editSmall\" onclick=\"document.location.href='updateUserForm.php?id={$user->getId()}'\">Edit</button>
						<button type=\"button\" class=\"deleteSmall\" onclick=\"deleteConfirmation('deleteUser.php?id={$user->getId()}');\">Delete</button>
					</td>
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
</div>
<?php
	include(APPLICATION_HOME."/includes/footer.inc");
	include(GLOBAL_INCLUDES."/xhtmlFooter.inc");
?>