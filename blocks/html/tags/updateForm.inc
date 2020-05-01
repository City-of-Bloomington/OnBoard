<?php
/**
 * @copyright 2017-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Tag $this->tag
 */
$id   = $this->tag->getId();
$name = parent::escape($this->tag->getName());


$title = $id ? $this->_('tag_edit') : $this->_('tag_add');
?>
<section>
    <header><h1><?= $title; ?></h1></header>
    <form method="post">
        <fieldset>
            <input name="id" type="hidden" value="<?= $id; ?>" />

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
                echo $helper->saveAndCancelButtons(parent::generateUri('tags.index'));
            ?>

        </fieldset>
    </form>
</section>
