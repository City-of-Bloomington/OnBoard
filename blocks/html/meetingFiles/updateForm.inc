<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param MeetingFile $this->meetingFile
 */
use Application\Models\MeetingFile;
use Blossom\Classes\Block;
use Blossom\Classes\Url;

$committee = $this->meetingFile->getCommittee();

$fields = ['id', 'type', 'title', 'filename', 'committee_id'];
foreach ($fields as $f) {
    $get = 'get'.ucfirst($f);
    $$f = parent::escape($this->meetingFile->$get());
}

$heading = $id ? $this->_('meetingFile_edit') : $this->_('meetingFile_add');
?>
<section>
    <header>
        <h1><?= $heading; ?></h1>
    </header>

    <form method="post" action="<?= BASE_URI; ?>/meetingFiles/update" enctype="multipart/form-data">
        <fieldset>
            <input name="meetingFile_id" type="hidden" value="<?= $id; ?>" />
            <input name="committee_id"   type="hidden" value="<?= $committee_id; ?>" />
            <?php
                $helper = $this->template->getHelper('field');

                $options = [];
                foreach (MeetingFile::$types as $t) { $options[] = ['value'=>$t]; }
                echo $helper->field([
                    'id'       => 'type',
                    'name'     => 'type',
                    'label'    => $this->_('type'),
                    'required' => true,
                    'value'    => $type,
                    'type'     => 'select',
                    'options'  => $options
                ]);

                echo $helper->field([
                    'id'    => 'title',
                    'name'  => 'title',
                    'label' => $this->_('title'),
                    'value' => $title
                ]);

                if ($committee->getCalendarId()) {
                    // They must choose from existing Events
                    $year = null;
                    if (!empty($_REQUEST['year'])) { $year = (int)$_REQUEST['year']; }
                    if (!$year) { $year = (int)$this->meetingFile->getMeetingDate('Y'); }
                    if (!$year) { $year = (int)date('Y'); }

                    $start = new \DateTime("$year-01-01");
                    $end   = new \DateTime("$year-01-01");
                    $end->add(new \DateInterval('P1Y'));

                    $url = new Url(Url::current_url(BASE_HOST));
                    $url->year = $year - 1;
                    $back = "<a href=\"$url\" class=\"button\">{$url->year}</a>";

                    $url->year = $year + 1;
                    $next = "<a href=\"$url\" class=\"button\">{$url->year}</a>";

                    $meetings = $committee->getMeetings($start, $end);
                    $options  = [['value'=>'']];
                    foreach ($meetings as $d=>$m) {
                        if (!empty($m['eventId'])) {
                            $date = new \DateTime($d);
                            $options[] = ['value'=>$m['eventId'], 'label'=>$date->format('F j Y')];
                        }
                    }

                    $select = $helper->select([
                        'id'       => 'eventId',
                        'name'     => 'eventId',
                        'type'     => 'select',
                        'value'    => $this->meetingFile->getEventId(),
                        'options'  => $options
                    ], true);
                    echo "
                    <dl class=\"required\">
                        <dt><label for=\"eventId\">{$this->_('meeting')}</label></dt>
                        <dd>$back $select $next
                            <div class=\"help\">
                                {$this->_('eventId_help', 'messages')}
                                <div>
                                    <a href=\"".GOOGLE_CALENDAR_URL."\" class=\"button\">Google Calendar</a>
                                </div>
                            </div>
                        </dd>
                    </dl>
                    ";
                }
                else {
                    // They can type whatever date they like
                    echo $helper->field([
                        'id'       => 'meetingDate',
                        'name'     => 'meetingDate',
                        'label'    => $this->_('meetingDate'),
                        'type'     => 'date',
                        'value'    => $this->meetingFile->getMeetingDate('U'),
                        'required' => true
                    ]);
                }

                $downloadUrl = BASE_URI."/meetingFiles/download?meetingFile_id=$id";
                $currentFile = $filename ? "<a href=\"$downloadUrl\">$filename</a>" : '';

                $accept = [];
                foreach (MeetingFile::$mime_types as $mime=>$ext) { $accept[] = ".$ext"; }

                $help = new Block('files/partials/uploadHelp.inc');
                echo $helper->field([
                    'id'    => 'meetingFile',
                    'name'  => 'meetingFile',
                    'label' => $this->_('meetingFile'),
                    'type'  => 'file',
                    'value' => $currentFile,
                    'attr'  => ['accept' => implode(',', $accept)],
                    'help'  => $help->render('html', $this->template)
                ]);

                $helper = $this->template->getHelper('saveAndCancelButtons');
                echo $helper->saveAndCancelButtons(BASE_URI.'/meetingFiles?committee_id='.$committee->getId());
            ?>
        </fieldset>
    </form>
</section>
