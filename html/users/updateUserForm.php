<?php
/*
	$_GET variables:	id
*/
	verifyUser("Administrator","Committee Member");

	include(GLOBAL_INCLUDES."/xhtmlHeader.inc");
	include(APPLICATION_HOME."/includes/banner.inc");
	include(APPLICATION_HOME."/includes/menubar.inc");
	include(APPLICATION_HOME."/includes/sidebar.inc");
?>
<div id="mainContent">
	<?php
		include(GLOBAL_INCLUDES."/errorMessages.inc");

		$user = new User($_GET['id']);
	?>
	<h1>Edit <?php echo $user->getUsername(); ?></h1>
	<form method="post" action="updateUser.php">
		<?php 
		
		if (in_array("Administrator", $_SESSION['USER']->getRoles())) 
		{
			echo 
			"<fieldset><legend>Login Info</legend>
			<table>
			<tr><td><label for=\"authenticationMethod\">Authentication</label></td>
					<td><select name=\"authenticationMethod\" id=\"authenticationMethod\">
			<option"; if ($user->getAuthenticationMethod()=='LDAP') {echo " selected=\"selected\"";}echo ">LDAP</option>";
			echo "<option"; if ($user->getAuthenticationMethod()=='local') {echo " selected=\"selected\"";} echo ">local</option>";
			echo	"</select></td></tr>
			<tr><td><label for=\"username\">Username</label></td>
				<td><input name=\"username\" id=\"username\" value=\"{$user->getUsername()}\" /></td></tr>
			<tr><td><label for=\"password\">Password</label></td>
				<td><input name=\"password\" id=\"password\" /></td></tr>
			<tr><td><label for=\"roles\">Roles</label></td>
				<td><select name=\"roles[]\" id=\"roles\" size=\"5\" multiple=\"multiple\">";
					$roles = new RoleList();
					$roles->find();
					foreach($roles as $role)
					{
						if (in_array($role,$user->getRoles())) { echo "<option selected=\"selected\">$role</option>"; }
						else { echo "<option>$role</option>"; }
					}
			echo "</select></td></tr>
			</table>
			</fieldset>";
			
			$cancel = "home.php";
		}
		else {  $cancel = BASE_URL; }
	?>
	<fieldset><legend>Personal Info</legend>
		<input name="id" type="hidden" value="<?php echo $user->getId(); ?>" />
		<table>
		<tr><td><label for="firstname">Firstname</label></td>
			<td><input name="firstname" id="firstname" value="<?php echo $user->getFirstname(); ?>" /></td></tr>
		<tr><td><label for="lastname">Lastname</label></td>
			<td><input name="lastname" id="lastname" value="<?php echo $user->getLastname(); ?>" /></td></tr>
		<tr><td><label for="email">Email</label></td>
			<td><input name="email" id="email" value="<?php echo $user->getEmail(); ?>" /></td></tr>
		<tr><td><label for="homephone">Home Phone</label></td>
			<td><input name="homephone" id="homephone" value="<?php echo $user->getHomePhone(); ?>" /></td></tr>
		<tr><td><label for="workphone">Work Phone</label></td>
				<td><input name="workphone" id="workphone" value="<?php echo $user->getWorkPhone(); ?>" /></td>
		</tr>
		<tr><td><label for="address">Address</label></td>
				<td><input name="address" id="address" value="<?php echo $user->getAddress(); ?>" /></td>
		</tr>	
		<tr><td><label for="city">City, State</label></td>
				<td><input name="city" id="city" value="<?php echo $user->getCity(); ?>" /></td>
		</tr>
		<tr><td><label for="zip">Zip Code</label></td>
				<td><input name="zip" id="zip" value="<?php echo $user->getZipcode(); ?>" /></td>
		</tr>
		<tr><td><label>About</label></td>
				<td><textarea cols="30" rows="5" name="about"><?php echo $user->getAbout(); ?></textarea></td>
		</tr>
		</table>

		<button type="submit" class="submit">Submit</button>
		<button type="button" class="cancel" onclick="document.location.href='<?php echo $cancel;?>';">Cancel</button>
	</fieldset>
	</form>
</div>

<?php
	include(APPLICATION_HOME."/includes/footer.inc");
	include(GLOBAL_INCLUDES."/xhtmlFooter.inc");
?>