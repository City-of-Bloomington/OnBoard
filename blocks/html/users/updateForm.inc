<?php
/**
 * @copyright 2006-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
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
	<form method="post">
		<fieldset><legend><?= $this->_(['user','users', 10]); ?></legend>
			<input name="user_id" type="hidden" value="<?= $id; ?>" />

            <?php
                $field = $this->template->getHelper('field');

                $options = [];
                foreach (Person::getAuthenticationMethods() as $m) { $options[] = ['value'=>$m]; }
                echo $field->field([
                    'name'     => 'authenticationMethod',
                    'id'       => 'authenticationMethod',
                    'label'    => $this->_('authenticationMethod'),
                    'value'    => $authenticationMethod,
                    'type'     => 'select',
                    'options'  => $options,
                    'required' => true
                ]);

                echo $field->field(['name'=>'username', 'id'=>'username', 'label'=>$this->_('username'), 'value'=>$username, 'required'=>true]);
                echo $field->field(['name'=>'password', 'id'=>'password', 'label'=>$this->_('password'), 'type'=>'password']);
				echo $field->field(['name'=>'email',    'id'=>'email',    'label'=>$this->_('email'   ), 'value'=>$email]);

				global $ACL;
				$options = [];
				foreach (array_reverse($ACL->getRoles()) as $r) { $options[] = ['value'=>$r]; }
				echo $field->field([
                    'name'     => 'role',
                    'id'       => 'role',
                    'label'    => $this->_('role'),
                    'value'    => $role,
                    'type'     => 'select',
                    'options'  => $options,
                    'required' => true
				]);

				$helper = $this->template->getHelper('saveAndCancelButtons');
				echo $helper->saveAndCancelButtons(parent::generateUri('users.index'));
			?>
		</fieldset>
	</form>
</section>
