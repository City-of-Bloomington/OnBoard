<?php
/**
 * @copyright 2009-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Race $this->race
 */
$race_id = $this->race->getId();
$name    = parent::escape($this->race->getName());
$title   = $race_id ? $this->_('race_add') : $this->_('race_add');
?>
<section>
	<h1><?= $title; ?></h1>
	<form method="post">
		<fieldset><legend><?= $name; ?></legend>
			<input name="race_id" type="hidden" value="<?= $race_id; ?>" />
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
				echo $helper->saveAndCancelButtons(parent::generateUri('races.index'));
			?>
		</fieldset>
	</form>
</section>
