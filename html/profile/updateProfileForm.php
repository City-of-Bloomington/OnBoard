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
	<h1>Edit Your Profile</h1>
	<form method="post" action="updateProfile.php">
		<fieldset><legend>Login Info</legend>
			<input name="id" type="hidden" value="<?php echo $user->getId(); ?>" />
			<table>
				<?php if ($user->getAuthenticationMethod() == "local")
							{ 
								echo
								"<tr><td><label for=\"uname\">Username</label></td>
										<td><input name=\"uname\" id=\"uname\" value=\"{$user->getUsername()}\" /></td>
								</tr>
								<tr><td><label for=\"pword\">Password</label></td>
									<td><input name=\"pword\" id=\"pword\" value=\"\" /></td>
								</tr>";
							}
				?>
				<tr><td><label for="fname">First Name</label></td>
						<td><input name="fname" id="fname" value="<?php echo $user->getFirstname(); ?>" /></td>
				</tr>
				<tr><td><label for="lname">Last Name</label></td>
						<td><input name="lname" id="lname" value="<?php echo $user->getLastname(); ?>" /></td>
				</tr>
				<tr><td><label for="email">Email Address</label></td>
						<td><input name="email" id="email" value="<?php echo $user->getEmail(); ?>" /></td>
				</tr>
				
				<tr><td><label for="homephone">Home Phone Number</label></td>
						<td><input name="homephone" id="homephone" value="<?php echo $user->getHomePhone(); ?>" /></td>
				</tr>
				
				<tr><td><label for="workphone">Work Phone Number</label></td>
						<td><input name="workphone" id="workphone" value="<?php echo $user->getWorkPhone(); ?>" /></td>
				</tr>
				
				<tr><td>Your Address</td></tr>
				<tr><td><label for="street">Street</label></td>
						<td><input name="street" id="street" value="<?php echo $user->getStreet(); ?>" /></td>
				</tr>
				
				<tr><td><label for="city">City, State</label></td>
						<td><input name="city" id="city" value="<?php echo $user->getCity(); ?>" /></td>
				</tr>
				
				<tr><td><label for="zip">Zip Code</label></td>
						<td><input name="zip" id="zip" value="<?php echo $user->getZipCode(); ?>" /></td>
				</tr>
				<tr><td><label>About Yourself</label></td>
						<td><textarea cols="30" rows="5" name="about"><?php echo $user->getAbout(); ?></textarea></td>
				</tr>
				
				<tr>
					<td></td><td><input type="submit" value="Submit Changes" /></td>
				</tr>
			</table>
		</fieldset>
	</form>
</div>

<?php
	include(APPLICATION_HOME."/includes/footer.inc");
	include(GLOBAL_INCLUDES."/xhtmlFooter.inc");
?>