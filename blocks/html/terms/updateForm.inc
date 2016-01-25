<?php
/**
 * @copyright 2009-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Term $this->term
 */

$title = $this->term->getId() ? $this->_('term_edit') : $this->_('term_add');

$picker = $this->template->getHelper('datePicker');
?>
<section    class="fn1-uiBlock">
    <header class="fn1-uiBlock-header">
        <h1 class="fn1-uiBlock-title"><?= $title; ?></h1>
    </header>
	<form   class="fn1-uiBlock-content" method="post" action="<?= BASE_URI; ?>/terms/update" >
		<input name="term_id" type="hidden" value="<?= $this->term->getId();      ?>" />
		<input name="seat_id" type="hidden" value="<?= $this->term->getSeat_id(); ?>" />

        <dl class="fn1-input-field">
            <dt><label  for="startDate" class="required"><?= $this->_('startDate'); ?></label></dt>
            <dd><?= $picker->datePicker('startDate', 'startDate', $this->term->getStartDate('U')); ?></dd>
        </dl>
        <?php
            if ($this->term->getId()) {
                echo "
                <dl class=\"fn1-input-field\">
                    <dt><label  for=\"endDate\" class=\"required\">{$this->_('endDate')}</label></dt>
                    <dd>{$picker->datePicker('endDate', 'endDate', $this->term->getEndDate('U'))}</dd>
                </dl>
                ";
            }
            
			$helper = $this->template->getHelper('saveAndCancelButtons');
			echo $helper->saveAndCancelButtons(BASE_URI.'/seats/view?seat_id='.$this->term->getSeat_id());
		?>
	</form>
</section>
