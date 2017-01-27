<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param MeetingFile $this->file
 */
use Application\Models\CommitteeTable;
use Application\Models\MeetingFile;

$title = $this->file->getId() ? $this->_('meetingFile_edit') : $this->_('meetingFile_add');
?>
<section>
    <header>
        <h1><?= $title; ?></h1>
    </header>

    <form method="post" action="<?= BASE_URI; ?>/meetingFiles/update" enctype="multipart/form-data">
        <fieldset>
            <input name="meetingFile_id" type="hidden" value="<?= $this->file->getId(); ?>" />
            <?php
                $helper = $this->template->getHelper('field');

                $options = [];
                $table   = new CommitteeTable();
                $list    = $table->find();
                foreach ($list as $c) {
                    $options[] = ['value'=>$c->getId(), 'label'=>parent::escape($c->getName())];
                }
                echo $helper->field([
                    'id'       => 'committee_id',
                    'name'     => 'committee_id',
                    'label'    => $this->_('committee'),
                    'required' => true,
                    'value'    => $this->file->getCommittee_id(),
                    'type'     => 'select',
                    'options'  => $options
                ]);

                $options = [];
                foreach (MeetingFile::$types as $t) { $options[] = ['value'=>$t]; }
                echo $helper->field([
                    'id'       => 'type',
                    'name'     => 'type',
                    'label'    => $this->_('type'),
                    'required' => true,
                    'value'    => $this->file->getType(),
                    'type'     => 'select',
                    'options'  => $options
                ]);

                echo $helper->field([
                    'id'    => 'eventId',
                    'name'  => 'eventId',
                    'label' => $this->_('eventId'),
                    'value' => $this->file->getEventId()
                ]);

                echo $helper->field([
                    'id'       => 'meetingDate',
                    'name'     => 'meetingDate',
                    'label'    => $this->_('date'),
                    'value'    => $this->file->getMeetingDate('U'),
                    'type'     => 'date',
                    'required' => true
                ]);

                echo $helper->field([
                    'id'    => 'meetingFile',
                    'name'  => 'meetingFile',
                    'label' => $this->_('meetingFile'),
                    'type'  => 'file'
                ]);

                $helper = $this->template->getHelper('saveAndCancelButtons');
                echo $helper->saveAndCancelButtons(BASE_URI.'/meetingFiles');
            ?>
        </fieldset>
    </form>
</section>
