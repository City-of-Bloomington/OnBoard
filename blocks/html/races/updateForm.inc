<?php
/**
 * @copyright 2009-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Race $this->race
 */
$name = parent::escape($this->race->getName());
$title = $this->race->getId() ? $this->_('race_add') : $this->_('race_add');
?>
<section>
	<h1><?= $title; ?></h1>
	<form method="post" action="<?= BASE_URI; ?>/races/update">
		<fieldset><legend><?= $name; ?></legend>
			<input name="race_id" type="hidden" value="<?= $this->race->getId(); ?>" />
			<?php
                $helper = $this->template->getHelper('field');
                echo $helper->field([
                    'name'     => 'name',
                    'id'       => 'name',
                    'label'    => $this->_('name'),
                    'value'    => $name,
                    'required' => true
                ]);

				$helper = $this->template->getHelper('saveAndCancelButtons');
				echo $helper->saveAndCancelButtons(BASE_URI.'/races');
			?>
		</fieldset>
	</form>
</section>
