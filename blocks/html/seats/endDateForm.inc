<?php
/**
 * @copyright 2016-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Seat $this->seat
 */
?>
<section>
    <header>
        <h1><?= $this->_('seat_end'); ?></h1>
    </header>
    <form method="post">
        <fieldset>
            <input name="seat_id" type="hidden" value="<?= $this->seat->getId(); ?>" />

            <?php
                $field = $this->template->getHelper('field');
                echo $field->field([
                    'label'    => $this->_('endDate'),
                    'name'     => 'endDate',
                    'id'       => 'endDate',
                    'value'    => new \DateTime(),
                    'type'     => 'date',
                    'required' => true
                ]);

                $helper = $this->template->getHelper('saveAndCancelButtons');
                echo $helper->saveAndCancelButtons(
                    parent::generateUri('seats.view').'?seat_id='.$this->seat->getId()
                );
            ?>
        </fieldset>
    </form>
</section>
