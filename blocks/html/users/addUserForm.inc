<?php
/**
 * @copyright Copyright (C) 2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
# Read in any values that might already be POSTed.
$fields = array('authenticationMethod','username',
				'firstname','lastname','email','address','city','zipcode',
				'homePhone','workPhone','about','photoPath');
foreach($fields as $field)
{
	$$field = isset($_POST['user'][$field]) ? View::escape($_POST['user'][$field]) : '';
}
?>
<h1>New User</h1>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
<fieldset><legend>Login Info</legend>
	<table>
	<tr><td><label for="user-authenticationMethod">Authentication</label></td>
		<td><select name="user[authenticationMethod]" id="user-authenticationMethod">
				<option <?php if($authenticationMethod=='LDAP') echo 'selected="selected"'; ?>>LDAP</option>
				<option <?php if($authenticationMethod=='local') echo 'selected="selected"'; ?>>local</option>
			</select>
		</td>
	</tr>
	<tr><td><label for="user-username">Username</label></td>
		<td><input name="user[username]" id="user-username" value="<?php echo $username; ?>" /></td></tr>
	<tr><td><label for="user-password">Password</label></td>
		<td><input name="user[password]" id="user-password" /></td></tr>
	<tr><td><label for="user[roles]">Roles</label></td>
		<td><select name="user[roles][]" id="user-roles" size="5" multiple="multiple">
			<?php
				$roles = new RoleList();
				$roles->find();
				foreach($roles as $role)
				{
					if (isset($_POST['user']['roles']) && in_array($role,$_POST['user']['roles']))
					{
						echo "<option selected=\"selected\">$role</option>";
					}
					echo "<option>$role</option>";
				}
			?>
			</select>
		</td>
	</tr>
	</table>

</fieldset>
<fieldset><legend>Personal Info</legend>
	<p>This information is only necessary for local users.
		For LDAP authentication, this information will be pulled from LDAP.</p>
	<table>
	<tr><td><label for="user-firstname">Firstname</label></td>
		<td><input name="user[firstname]" id="user-firstname" value="<?php echo $firstname; ?>" /></td></tr>

	<tr><td><label for="user-lastname">Lastname</label></td>
		<td><input name="user[lastname]" id="user-lastname" value="<?php echo $lastname; ?>" /></td></tr>

	<tr><td><label for="user-email">Email</label></td>
		<td><input name="user[email]" id="user-email" value="<?php echo $email; ?>" /></td></tr>

	<tr><td><label for="user-address">Address</label></td>
		<td><input name="user[address]" id="user-address" value="<?php echo $address; ?>" /></td></tr>

	<tr><td><label for="user-city">City</label></td>
		<td><input name="user[city]" id="user-city" value="<?php echo $city; ?>" /></td></tr>

	<tr><td><label for="user-zipcode">Zip</label></td>
		<td><input name="user[zipcode]" id="user-zipcode" value="<?php echo $zipcode; ?>" /></td></tr>

	<tr><td><label for="user-homePhone">Home Phone</label></td>
		<td><input name="user[homePhone]" id="user-homePhone" value="<?php echo $homePhone; ?>" /></td></tr>

	<tr><td><label for="user-workPhone">Work Phone</label></td>
		<td><input name="user[workPhone]" id="user-workPhone" value="<?php echo $workPhone; ?>" /></td></tr>

	<tr><td><label for="user-about">About</label></td>
		<td><textarea name="user[about]" id="user-about" rows="3" cols="60"><?php echo $about; ?></textarea></td></tr>

	<tr><td><label for="user-photoPath">photoPath</label></td>
		<td><input name="user[photoPath]" id="user-photoPath" value="<?php echo $photoPath; ?>" /></td></tr>

	</table>

	<button type="submit" class="submit">Submit</button>
	<a class="cancel button" href="<?php echo BASE_URL; ?>/users">Cancel</a>
</fieldset>
</form>
