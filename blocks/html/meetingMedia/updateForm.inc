<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param MeetingMedia $this->media
 */
use Application\Models\CommitteeTable;
use Application\Models\MeetingMedia;

$title = $this->media->getId() ? $this->_('meetingMedia_edit') : $this->_('meetingMedia_add');
?>
<section>
    <header>
        <h1><?= $title; ?></h1>
    </header>

    <form method="post" action="<?= BASE_URI; ?>/meetingMedia/update" enctype="multipart/form-data">
        <fieldset>
            <input name="meetingMedia_id" type="hidden" value="<?= $this->media->getId(); ?>" />
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
                    'value'    => $this->media->getCommittee_id(),
                    'type'     => 'select',
                    'options'  => $options
                ]);

                $options = [];
                foreach (MeetingMedia::$types as $t) { $options[] = ['value'=>$t]; }
                echo $helper->field([
                    'id'       => 'type',
                    'name'     => 'type',
                    'label'    => $this->_('type'),
                    'required' => true,
                    'value'    => $this->media->getType(),
                    'type'     => 'select',
                    'options'  => $options
                ]);

                echo $helper->field([
                    'id'    => 'eventId',
                    'name'  => 'eventId',
                    'label' => $this->_('eventId'),
                    'value' => $this->media->getEventId()
                ]);

                echo $helper->field([
                    'id'       => 'meetingDate',
                    'name'     => 'meetingDate',
                    'label'    => $this->_('date'),
                    'value'    => $this->media->getMeetingDate('U'),
                    'type'     => 'date',
                    'required' => true
                ]);

                echo $helper->field([
                    'id'    => 'mediafile',
                    'name'  => 'mediafile',
                    'label' => $this->_('media'),
                    'type'  => 'file'
                ]);

                $helper = $this->template->getHelper('saveAndCancelButtons');
                echo $helper->saveAndCancelButtons(BASE_URI.'/meetingMedia');
            ?>
        </fieldset>
    </form>
</section>
