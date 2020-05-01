<?php
/**
 * @copyright 2016-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Committee $this->committee
 */
?>
<section>
    <header>
        <h1><?= $this->_('committee_end'); ?></h1>
    </header>
    <form method="post" action="<?= BASE_URI; ?>/committees/end">
        <fieldset>
            <input name="committee_id" type="hidden" value="<?= $this->committee->getId(); ?>" />

            <?php
                $field = $this->template->getHelper('field');
                echo $field->field([
                    'label'    => $this->_('endDate'),
                    'name'     => 'endDate',
                    'id'       => 'endDate',
                    'value'    => time(),
                    'type'     => 'date',
                    'required' => true
                ]);

                $helper = $this->template->getHelper('saveAndCancelButtons');
                echo $helper->saveAndCancelButtons(
                    parent::generateUri('committees.info').'?committee_id='.$this->committee->getId()
                );
            ?>
        </fieldset>
    </form>
</section>
