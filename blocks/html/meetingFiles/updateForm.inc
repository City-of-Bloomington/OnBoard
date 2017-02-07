<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param MeetingFile $this->meetingFile
 */
use Application\Models\MeetingFile;

$title = $this->meetingFile->getId() ? $this->_('meetingFile_edit') : $this->_('meetingFile_add');
?>
<section>
    <header>
        <h1><?= $title; ?></h1>
    </header>

    <form method="post" action="<?= BASE_URI; ?>/meetingFiles/update" enctype="multipart/form-data">
        <fieldset>
            <input name="meetingFile_id" type="hidden" value="<?= $this->meetingFile->getId(); ?>" />
            <?php
                $helper = $this->template->getHelper('field');

                $options = [];
                foreach (MeetingFile::$types as $t) { $options[] = ['value'=>$t]; }
                echo $helper->field([
                    'id'       => 'type',
                    'name'     => 'type',
                    'label'    => $this->_('type'),
                    'required' => true,
                    'value'    => $this->meetingFile->getType(),
                    'type'     => 'select',
                    'options'  => $options
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
