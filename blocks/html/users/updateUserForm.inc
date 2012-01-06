<?php
/**
 * @copyright 2006-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param User $this->user
 */
?>
<h1>Edit <?php echo $this->user->getUsername(); ?></h1>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
	<fieldset><legend>Login Info</legend>
		<input name="user_id" type="hidden" value="<?php echo $this->user->getId(); ?>" />
		<table>
			<tr><td><label for="user-authenticationMethod">Authentication</label></td>
				<td><select name="user[authenticationMethod]" id="user-authenticationMethod">
					<?php
						foreach (User::getAuthenticationMethods() as $method) {
							$selected = $this->user->getAuthenticationMethod()==$method
								? 'selected="selected"'
								: '';
							echo "<option $selected>$method</option>";
						}
					?>
					</select>
				</td>
			</tr>
			<tr><td><label for="user-username">Username</label></td>
				<td><input name="user[username]" id="user-username" value="<?php echo View::escape($this->user->getUsername()); ?>" />
				</td>
			</tr>
			<tr><td><label for="user-password">Password</label></td>
				<td><input name="user[password]" id="user-password" /></td>
			</tr>
			<tr><td><label for="user-roles">Roles</label></td>
				<td><select name="user[roles][]" id="user-roles" size="5" multiple="multiple">
					<?php
						$roles = new RoleList();
						$roles->find();
						foreach ($roles as $role) {
							$selected = in_array($role,$this->user->getRoles())
										? 'selected="selected"'
										: '';
							echo "<option $selected>$role</option>";
						}
					?>
					</select>
				</td>
			</tr>
		</table>
		<button type="submit" class="submit">Submit</button>
		<button type="button" class="cancel" onclick="document.location.href='<?php echo BASE_URL; ?>/users';">
			Cancel
		</button>
	</fieldset>
</form>
