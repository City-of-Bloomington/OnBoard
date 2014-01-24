<form method="post" action="<?php echo BASE_URI; ?>/login/login">
	<fieldset><legend><?php echo $this->_('labels.login'); ?></legend>
		<input name="return_url" type="hidden" value="<?php echo View::escape($this->return_url); ?>" />
		<table>
			<tr><th><label for="username"><?php echo $this->_('labels.username'); ?></label></th>
				<td><input name="username" id="username" /></td><td></td>
			</tr>
			<tr><th><label for="password"><?php echo $this->_('labels.password'); ?></label></th>
				<td><input type="password" name="password" id="password" /></td>
			</tr>
		</table>
		<button type="submit" class="login"><?php echo $this->_('labels.login'); ?></button>
	</fieldset>
</form>
