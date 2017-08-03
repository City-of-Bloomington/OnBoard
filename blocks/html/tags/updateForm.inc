<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Tag $this->tag
 */
$id   = $this->tag->getId();
$name = parent::escape($this->tag->getName());


$title = $id ? $this->_('tag_edit') : $this->_('tag_add');
?>
<section>
    <header><h1><?= $title; ?></h1></header>
    <form method="post" action="<?= BASE_URI; ?>/tags/update">
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
                echo $helper->saveAndCancelButtons(BASE_URI.'/tags');
            ?>

        </fieldset>
    </form>
</section>
