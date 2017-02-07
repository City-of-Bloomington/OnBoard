<form method="post" action="<?= BASE_URI; ?>/login/login">
	<fieldset><legend><?= $this->_('login'); ?></legend>
		<input name="return_url" type="hidden" value="<?= self::escape($this->return_url); ?>" />

		<?php
            $h = $this->template->getHelper('field');
            echo $h->field(['name'=>'username', 'id'=>'username', 'label'=>$this->_('username'), 'required'=>true]);
            echo $h->field(['name'=>'password', 'id'=>'password', 'label'=>$this->_('password'), 'required'=>true, 'type'=>'password']);
		?>

		<button type="submit" class="login"><?= $this->_('login'); ?></button>
	</fieldset>
</form>
