<?php
/**
 * @copyright 2009-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Race $this->appointer
 */
$name  = self::escape($this->appointer->getName());
$title = $this->appointer->getId() ? $this->_('edit_appointer') : $this->_('add_appointer');
?>
<section>
    <header>
        <h1><?= $title; ?></h1>
    </header>
	<form method="post">
		<fieldset><legend><?= $name; ?></legend>
			<input name="appointer_id" type="hidden" value="<?= $this->appointer->getId(); ?>" />

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
				echo $helper->saveAndCancelButtons(parent::generateUri('appointers.index'));
			?>
		</fieldset>
	</form>
</section>
