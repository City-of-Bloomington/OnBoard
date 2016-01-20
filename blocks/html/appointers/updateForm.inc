<?php
/**
 * @copyright 2009-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Race $this->appointer
 */
$name = self::escape($this->appointer->getName());
$title = $this->appointer->getId() ? $this->_('edit_appointer') : $this->_('add_appointer');
?>
<section>
	<h1><?= $title; ?></h1>
	<form method="post" action="<?= BASE_URI; ?>/appointers/update">
		<fieldset><legend><?= $name; ?></legend>
			<input name="appointer_id" type="hidden" value="<?= $this->appointer->getId(); ?>" />

            <dl class="fn1-input-field">
                <dt><label  for="name" class="required"><?= $this->_('name'); ?></label></dt>
                <dd><input name="name" id="name" value="<?= $name; ?>" /></dd>
            </dl>

			<?php
				$helper = $this->template->getHelper('saveAndCancelButtons');
				echo $helper->saveAndCancelButtons(BASE_URI.'/appointers');
			?>
		</fieldset>
	</form>
</section>
