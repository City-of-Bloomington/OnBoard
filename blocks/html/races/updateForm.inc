<?php
/**
 * @copyright 2009-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Race $this->race
 */
use Blossom\Classes\View;

$name = View::escape($this->race->getName());
$title = $this->race->getId() ? $this->_('edit_race') : $this->_('add_race');
?>
<section>
	<h1><?= $title; ?></h1>
	<form method="post" action="<?= BASE_URI; ?>/races/update">
		<fieldset><legend><?= $name; ?></legend>
			<input name="race_id" type="hidden" value="<?= $this->race->getId(); ?>" />

            <dl class="fn1-input-field">
                <dt><label for="name" class="required"><?= $this->_('name'); ?></label></dt>
                <dd><input name="name" id="name" value="<?= $name; ?>" /></dd>
            </dl>

			<?php
				$helper = $this->template->getHelper('saveAndCancelButtons');
				echo $helper->saveAndCancelButtons(BASE_URI.'/races');
			?>
		</fieldset>
	</form>
</section>