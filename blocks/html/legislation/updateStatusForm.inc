<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Status $this->status
 */
$id      = $this->status->getId();
$name    = parent::escape($this->status->getName());

$title = $id ? $this->_('legislationStatus_edit') : $this->_('legislationStatus_add');
?>
<section>
    <header>
        <h1><?= $title; ?></h1>
    </header>
    <form method="post" action="<?= BASE_URI; ?>/legislationStatuses/update">
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

                $h = $this->template->getHelper('saveAndCancelButtons');
                echo $h->saveAndCancelButtons(BASE_URI.'/legislationStatuses');
            ?>
        </fieldset>
    </form>
</section>
