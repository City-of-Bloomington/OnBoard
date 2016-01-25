<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Department $this->department
 */
$fields = ['name', 'email', 'phone'];
foreach ($fields as $f) {
    $get = 'get'.ucfirst($f);
    $$f = self::escape($this->department->$get());
}
$name = self::escape($this->department->getName());
$title = $this->department->getId() ? $this->_('department_edit') : $this->_('department_add');
?>
<section>
	<h1><?= $title; ?></h1>
	<form method="post" action="<?= BASE_URI; ?>/departments/update">
		<fieldset><legend><?= $name; ?></legend>
			<input name="department_id" type="hidden" value="<?= $this->department->getId(); ?>" />

            <dl class="fn1-input-field">
                <dt><label for="name" class="required"><?= $this->_('name'); ?></label></dt>
                <dd><input name="name" id="name" value="<?= $name; ?>" /></dd>
            </dl>

            <dl class="fn1-input-field">
                <dt><label  for="email"><?= $this->_('email'); ?></label></dt>
                <dd><input name="email" id="email" value="<?= $email; ?>" /></dd>
            </dl>

            <dl class="fn1-input-field">
                <dt><label  for="phone"><?= $this->_('phone'); ?></label></dt>
                <dd><input name="phone" id="phone" type="tel" value="<?= $phone; ?>" /></dd>
            </dl>

			<?php
				$helper = $this->template->getHelper('saveAndCancelButtons');
				echo $helper->saveAndCancelButtons(BASE_URI.'/departments');
			?>
		</fieldset>
	</form>
</section>