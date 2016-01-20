<?php
/**
 * @copyright 2006-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param User $this->user
 */
use Application\Models\Person;
$fields = ['id', 'authenticationMethod', 'role', 'username'];
foreach ($fields as $f) {
    $get = 'get'.ucfirst($f);
    $$f = self::escape($this->user->$get());
}
?>
<section>
	<form method="post" action="<?= BASE_URI; ?>/users/update">
		<fieldset><legend><?= $this->_(['user','users',2]); ?></legend>
			<input name="user_id" type="hidden" value="<?= $id; ?>" />

            <dl class="fn1-input-field">
                <dt><label   for="authenticationMethod"><?= $this->_('authenticationMethod'); ?></label></dt>
                <dd><select name="authenticationMethod" id="authenticationMethod">
                    <?php
                        foreach (Person::getAuthenticationMethods() as $method) {
                            $selected = $authenticationMethod === $method
                                ? 'selected="true"'
                                : '';
                            echo "<option $selected>$method</option>";
                        }
                    ?>
                    </select>
                </dd>
            </dl>
            <dl class="fn1-input-field">
                <dt><label  for="username"><?= $this->_('username'); ?></label></dt>
                <dd><input name="username" id="username" value="<?= $username; ?>" /></dd>
            </dl>
            <dl class="fn1-input-field">
                <dt><label  for="password"><?= $this->_('password'); ?></label></dt>
                <dd><input name="password" id="password" /></dd>
            </dl>
            <dl class="fn1-input-field">
                <dt><label   for="role"><?= $this->_('role'); ?></label></dt>
                <dd><select name="role" id="role"><option></option>
                    <?php
                        global $ZEND_ACL;
                        foreach (array_reverse($ZEND_ACL->getRoles()) as $r) {
                            $selected = $role === $r
                                ? 'selected="true"'
                                : '';
                            echo "<option $selected>$r</option>";
                        }
                    ?>
                    </select>
                </dd>
            </dl>
			<?php
				$helper = $this->template->getHelper('saveAndCancelButtons');
				echo $helper->saveAndCancelButtons(BASE_URI.'/users');
			?>
		</fieldset>
	</form>
</section>
