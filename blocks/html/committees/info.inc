<?php
/**
 * @copyright 2014-2022 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Committee $this->committee
 */
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;
use Web\View;
use Michelf\Markdown;

$fields = [
    'id', 'type',
    'name', 'statutoryName', 'code', 'yearFormed', 'website', 'videoArchive', 'calendarId',
    'email', 'phone', 'address', 'city', 'state', 'zip',
    'termEndWarningDays', 'applicationLifetime', 'legislative'
];
foreach ($fields as $f) {
    $get = 'get'.ucfirst($f);
    $$f = View::escape($this->committee->$get());
    if ($f == 'website' || $f == 'videoArchive') {
        $$f = "<a href=\"{$$f}\">{$$f}</a>";
    }
}
$description     = $this->committee->getDescription()
                 ? Markdown::defaultTransform($this->committee->getDescription())
                 : '';
$meetingSchedule = $this->committee->getMeetingSchedule()
                 ? Markdown::defaultTransform($this->committee->getMeetingSchedule())
                 : '';

$helper      = $this->template->getHelper('buttonLink');
$editButton  = '';
$endButton   = '';
$userCanEdit = Person::isAllowed('committees', 'update');
if ($userCanEdit) {
    $editButton = $helper->buttonLink(
        parent::generateUri('committees.update')."?committee_id=$id",
         $this->_('edit'),
        'edit'
    );
}

$endDate = $this->committee->getEndDate('U');
if ((!$endDate || $endDate > time())
    && Person::isAllowed('committees', 'end')) {

    $endButton = $helper->buttonLink(
        parent::generateUri('committees.end')."?committee_id=$id",
        $this->_('committee_end'),
        'delete'
    );
}
?>
<section>
    <header>
        <h2><?= $this->_('committee_info') ?></h2>
        <div class="tools"><?= "$editButton $endButton"; ?></div>
    </header>
    <div>
    <?php
        $fields  = [
            'type', 'yearFormed', 'endDate',
            'statutoryName', 'code', 'website', 'videoArchive',
            'email', 'phone', 'address', 'calendarId', 'legislative'
        ];
        if ($userCanEdit) {
            $fields[] = 'termEndWarningDays';
            $fields[] = 'applicationLifetime';
        }
        $address = $address ? "<address>$address\n$city $state $zip</address>" : null;

        $endDate = $this->committee->getEndDate(DATE_FORMAT);

        echo '<table>';
        foreach ($fields as $f) {
            if ($$f) {
                switch ($f) {
                    case 'calendarId':
                        $$f = "<a href=\"https://calendar.google.com/calendar/embed?src={$$f}\">{$$f}</a>";
                    break;
                    case 'termEndWarningDays':
                        $$f = $$f.' '.$this->_(['day', 'days', $$f]);
                    break;
                    case 'legislative':
                        $$f = $$f ? $this->_('yes') : $this->_('no');
                    break;
                }
                echo "
                <tr>
                    <th>{$this->_($f)}</th>
                    <td>{$$f}</td>
                </dl>
                ";
            }
        }
        echo '</table>';
    ?>
    <?= $description; ?>

    <h2><?= $this->_('meetingSchedule'); ?></h2>
    <?= $meetingSchedule; ?>
    </div>
</section>
