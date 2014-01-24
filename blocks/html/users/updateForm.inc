<?php
/**
 * @copyright 2006-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param User $this->user
 */
use Application\Models\Person;
use Blossom\Classes\View;

?>
<div class="updateUserForm">
	<form method="post" action="<?php echo BASE_URI; ?>/users/update">
		<fieldset><legend><?php echo $this->_(['labels.user','labels.users',2]); ?></legend>
			<input name="user_id" type="hidden" value="<?php echo $this->user->getId(); ?>" />
			<table>
				<tr><td><label for="authenticationMethod"><?php echo $this->_('labels.authenticationMethod'); ?></label></td>
					<td><select name="authenticationMethod" id="authenticationMethod">
						<?php
							foreach (Person::getAuthenticationMethods() as $method) {
								$selected = $this->user->getAuthenticationMethod()==$method
									? 'selected="selected"'
									: '';
								echo "<option $selected>$method</option>";
							}
						?>
						</select>
					</td>
				</tr>
				<tr><td><label for="username"><?php echo $this->_('labels.username'); ?></label></td>
					<td><input name="username" id="username" value="<?php echo View::escape($this->user->getUsername()); ?>" />
					</td>
				</tr>
				<tr><td><label for="password"><?php echo $this->_('labels.password'); ?></label></td>
					<td><input name="password" id="password" /></td>
				</tr>
				<tr><td><label for="role"><?php echo $this->_('labels.role'); ?></label></td>
					<td><select name="role" id="role"><option></option>
						<?php
							global $ZEND_ACL;
							foreach (array_reverse($ZEND_ACL->getRoles()) as $role) {
								$selected = $this->user->getRole()==$role
									? 'selected="selected"'
									: '';
								echo "<option $selected>$role</option>";
							}
						?>
						</select>
					</td>
				</tr>
			</table>
			<button type="submit" class="submit"><?php echo $this->_('labels.submit'); ?></button>
			<a class="cancel" href="<?php echo BASE_URI; ?>/users">
				<?php echo $this->_('labels.cancel'); ?>
			</a>
		</fieldset>
	</form>
</div>
