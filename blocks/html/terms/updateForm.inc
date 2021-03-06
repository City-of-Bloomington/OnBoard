<?php
/**
 * @copyright 2009-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE
 * @param Term $this->term
 */
$title  = $this->term->getId() ? $this->_('term_edit') : $this->_('term_add');
$helper = $this->template->getHelper('field');
?>
<section>
    <header>
        <h1><?= $title; ?></h1>
    </header>
	<form method="post">
		<input name="term_id" type="hidden" value="<?= $this->term->getId();      ?>" />
		<input name="seat_id" type="hidden" value="<?= $this->term->getSeat_id(); ?>" />
		<?php
            echo $helper->field([
                'name'     => 'startDate',
                'id'       => 'startDate',
                'label'    => $this->_('startDate'),
                'value'    => new \DateTime($this->term->getStartDate()),
                'type'     => 'date',
                'required' => true
            ]);
            if ($this->term->getId()) {
                echo $helper->field([
                    'name'     => 'endDate',
                    'id'       => 'endDate',
                    'label'    => $this->_('endDate'),
                    'value'    => new \DateTime($this->term->getEndDate()),
                    'type'     => 'date',
                    'required' => true
                ]);
            }

			$helper = $this->template->getHelper('saveAndCancelButtons');
			echo $helper->saveAndCancelButtons(parent::generateUri('seats.view').'?seat_id='.$this->term->getSeat_id());
		?>
	</form>
</section>
