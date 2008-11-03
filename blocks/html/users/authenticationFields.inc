<?php
/**
 * @copyright Copyright (C) 2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param User $this->user
 */
$authenticationMethod = $this->user->getAuthenticationMethod();
$username = View::escape($this->user->getUsername());
?>
<fieldset><legend>Login Info</legend>
	<p>You only need to fill in this information, if you want the person to be able to log in.</p>
	<p>If you are adding an LDAP-authenticated user, only the top portion of the form is necessary.
	Their personal information will be populated automatically from LDAP.</p>
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
				$roles = $this->user->getRoles();

				$list = new RoleList();
				$list->find();
				foreach($list as $role)
				{
					$selected = in_array($role,$roles) ? ' selected="selected"' : '';
					echo "<option$selected>$role</option>";
				}
			?>
			</select>
		</td>
	</tr>
	</table>
</fieldset>
