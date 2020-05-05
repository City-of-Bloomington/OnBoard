<?php
/**
 * @copyright 2009-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE
 * @param Office $this->office
 */
$startDate = new \DateTime($this->office->getStartDate());
$endDate   = $this->office->getEndDate()
             ? new \DateTime($this->office->getEndDate())
             : null;
$title = $this->office->getId() ? $this->_('office_edit') : $this->_('office_add');
$helper = $this->template->getHelper('field');
?>
<section>
    <header>
        <h1><?= $title; ?></h1>
        <h2><?= self::escape($this->office->getPerson()->getFullname()); ?></h2>
    </header>
    <form method="post">
        <fieldset><legend><?= $this->_('info_office'); ?></legend>
            <input name="office_id"    type="hidden" value="<?= $this->office->getId(); ?>" />
            <input name="committee_id" type="hidden" value="<?= $this->office->getCommittee_id(); ?>" />
            <input name="person_id"    type="hidden" value="<?= $this->office->getPerson_id(); ?>" />
            <?php
                echo $helper->field([
                    'name'     => 'title',
                    'id'       => 'title',
                    'label'    => $this->_('title'),
                    'value'    => parent::escape($this->office->getTitle()),
                    'required' => true
                ]);

                echo $helper->field([
                    'name'     => 'startDate',
                    'id'       => 'startDate',
                    'label'    => $this->_('startDate'),
                    'value'    => $startDate,
                    'type'     => 'date',
                    'required' => true
                ]);
                echo $helper->field([
                    'name'     => 'endDate',
                    'id'       => 'endDate',
                    'label'    => $this->_('endDate'),
                    'value'    => $endDate,
                    'type'     => 'date'
                ]);

                $helper = $this->template->getHelper('saveAndCancelButtons');
                echo $helper->saveAndCancelButtons(
                    parent::generateUri('committees.members').'?committee_id='.$this->office->getCommittee_id()
                );
            ?>
        </fieldset>
    </form>
</section>
