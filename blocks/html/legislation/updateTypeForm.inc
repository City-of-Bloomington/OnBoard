<?php
/**
 * @copyright 2017-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param LegislationType $this->type
 */
$id      = $this->type->getId();
$name    = parent::escape($this->type->getName());
$subtype = $this->type->getSubtype();

$title = $id ? $this->_('legislationType_edit') : $this->_('legislationType_add');
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
                    'name'    => 'subtype',
                    'label'   => $this->_('subtype'),
                    'value'   => $subtype,
                    'type'    => 'radio',
                    'options' => [
                        ['value'=>1, 'label'=>$this->_('yes')],
                        ['value'=>0, 'label'=>$this->_('no' )]
                    ]
                ]);

                $h = $this->template->getHelper('saveAndCancelButtons');
                echo $h->saveAndCancelButtons(parent::generateUri('legislationTypes.index'));
            ?>
        </fieldset>
    </form>
</section>
