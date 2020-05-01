<?php
/**
 * @copyright 2017-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param LegislationActionType $this->type
 */
$id       = $this->type->getId();
$name     = parent::escape($this->type->getName());
$ordering = $this->type->getOrdering();

$title = $id ? $this->_('legislationActionType_edit') : $this->_('legislationActionType_add');
?>
<section>
    <header>
        <h1><?= $title; ?></h1>
    </header>
    <form method="post">
        <fieldset>
            <input name="id" type="hidden" value="<?= $id; ?>" />
            <?php
                $h = $this->template->getHelper('field');
                echo $h->field([
                    'name'     => 'name',
                    'id'       => 'name',
                    'label'    => $this->_('name'),
                    'value'    => $name,
                    'required' => true
                ]);

                echo $h->field([
                    'name'     => 'ordering',
                    'id'       => 'ordering',
                    'label'    => $this->_('ordering'),
                    'value'    => $ordering
                ]);

                $h = $this->template->getHelper('saveAndCancelButtons');
                echo $h->saveAndCancelButtons(parent::generateUri('legislationActionTypes.index'));
            ?>
        </fieldset>
    </form>
</section>
