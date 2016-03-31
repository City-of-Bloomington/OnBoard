<?php
/**
 * @copyright 2006-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param User $this->user
 */
use Application\Models\Person;
$fields = ['id', 'authenticationMethod', 'role', 'username', 'email'];
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
                <dd><select name="authenticationMethod" id="authenticationMethod" required="true">
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
            <?php
                $field = $this->template->getHelper('field');
                echo $field->field(['name'=>'username', 'id'=>'username', 'label'=>$this->_('username'), 'value'=>$username, 'required'=>true]);
                echo $field->field(['name'=>'password', 'id'=>'password', 'label'=>$this->_('password'), 'type'=>'password']);
				echo $field->field(['name'=>'email',    'id'=>'email',    'label'=>$this->_('email'   ), 'value'=>$email]);
            ?>
            <dl class="fn1-input-field">
                <dt><label   for="role"><?= $this->_('role'); ?></label></dt>
                <dd><select name="role" id="role" required="true"><option></option>
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
