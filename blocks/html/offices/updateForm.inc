<?php
/**
 * @copyright 2009-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @param Office $this->office
 */

$title = $this->office->getId() ? $this->_('office_edit') : $this->_('office_add');
$picker = $this->template->getHelper('datePicker');
?>
<section>
    <header>
        <h1><?= $title; ?></h1>
        <h2><?= self::escape($this->office->getPerson()->getFullname()); ?></h2>
    </header>
    <form method="post" action="<?= BASE_URI; ?>/offices/update">
        <fieldset><legend><?= $this->_('info_office'); ?></legend>
            <input name="office_id"    type="hidden" value="<?= $this->office->getId(); ?>" />
            <input name="committee_id" type="hidden" value="<?= $this->office->getCommittee_id(); ?>" />
            <input name="person_id"    type="hidden" value="<?= $this->office->getPerson_id(); ?>" />

            <dl class="fn1-input-field">
                <dt><label  for="title"><?= $this->_('title'); ?></label></dt>
                <dd><input name="title" id="title"
                            value="<?= self::escape($this->office->getTitle()); ?>" />
                </dd>
            </dl>
            <dl class="fn1-input-field">
                <dt><label for="startDate"><?= $this->_('startDate'); ?></label></dt>
                <dd><?= $picker->datePicker('startDate', 'startDate', $this->office->getStartDate('U')); ?></dd>
            </dl>
            <dl class="fn1-input-field">
                <dt><label for="endDate"><?= $this->_('endDate'); ?></label></dt>
                <dd><?= $picker->datePicker('endDate', 'endDate', $this->office->getEndDate('U')); ?></dd>
            </dl>
            <?php
                $helper = $this->template->getHelper('saveAndCancelButtons');
                echo $helper->saveAndCancelButtons(
                    BASE_URI.'/committees/members?committee_id='.$this->office->getCommittee_id()
                );
            ?>
        </fieldset>
    </form>
</section>
