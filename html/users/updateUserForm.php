<?php
/*
	$_GET variables:	id
*/
	verifyUser("Administrator","Supervisor");

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
		<fieldset><legend>Login Info</legend>
			<input name="id" type="hidden" value="<?php echo $user->getId(); ?>" />
			<table>
				<tr><td><label for="authenticationMethod">Authentication</label></td>
					<td>
					 <select name="authenticationMethod" id="authenticationMethod">
						 <option <?php if ($user->getAuthenticationMethod() == 'LDAP') {echo "selected=\"selected\"";} ?>>LDAP</option>
					 	 <option <?php if ($user->getAuthenticationMethod() == 'local') {echo "selected=\"selected\"";} ?>>local</option>
					</select>
					</td>
				</tr>
				<tr><td><label for="uname">Username</label></td>
						<td><input name="uname" id="uname" value="<?php echo $user->getUsername(); ?>" /></td>
				</tr>
				<tr><td><label for="pword">Password</label></td>
						<td><input name="pword" id="pword" value="" /></td>
				</tr>
				<tr><td><label for="roles">Roles</label></td>
						<td><select name="roles[]" id="roles" size="5" multiple="multiple" >
								<?php 
								  $roleList = new RoleList();
								  $roleList->find();
								  foreach($roleList as $role) 
								  {
										echo "<option"; 
										if (in_array($role, $user->getRoles())){ echo " selected=\"selected\"";}
										echo ">$role</option>";
									}
								?> 	
							</select>		
						</td>
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