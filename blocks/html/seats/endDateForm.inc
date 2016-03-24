<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Seat $this->seat
 */
?>
<section>
    <header>
        <h1><?= $this->_('seat_end'); ?></h1>
    </header>
    <form method="post" action="<?= BASE_URI; ?>/seats/end">
        <fieldset>
            <input name="seat_id" type="hidden" value="<?= $this->seat->getId(); ?>" />

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
                    BASE_URI.'/seats/view?seat_id='.$this->seat->getId()
                );
            ?>
        </fieldset>
    </form>
</section>