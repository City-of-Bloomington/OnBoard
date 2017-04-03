<?php
/**
 * @copyright 2014-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Committee $this->committee
 */
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;
use Blossom\Classes\View;
use Michelf\MarkdownExtra;

$fields = [
    'id', 'type',
    'name', 'statutoryName', 'yearFormed', 'website', 'calendarId',
    'email', 'phone', 'address', 'city', 'state', 'zip', 'termEndWarningDays', 'applicationLifetime'
];
foreach ($fields as $f) {
    $get = 'get'.ucfirst($f);
    $$f = View::escape($this->committee->$get());
}

if ($website) {
    $website = "<a href=\"$website\">$website</a>";
}

$markdown = new MarkdownExtra();
$description     = $markdown->transform($this->committee->getDescription());
$meetingSchedule = $markdown->transform($this->committee->getMeetingSchedule());

$helper = $this->template->getHelper('buttonLink');
$editButton = '';
$endButton  = '';
$userCanEdit = Person::isAllowed('committees', 'edit');
if ($userCanEdit) {
    $editButton = $helper->buttonLink(
        BASE_URI."/committees/update?committee_id=$id",
         $this->_('edit'),
        'edit'
    );
}

$endDate = $this->committee->getEndDate('U');
if ((!$endDate || $endDate > time())
    && Person::isAllowed('committees', 'end')) {

    $endButton = $helper->buttonLink(
        BASE_URI."/committees/end?committee_id=$id",
        $this->_('committee_end'),
        'delete'
    );
}
?>
<section>
    <header>
        <h1><?= $this->_('committee_info') ?></h1>
        <div class="tools"><?= "$editButton $endButton"; ?></div>
    </header>
    <div>
    <?php
        $fields  = [
            'type', 'yearFormed', 'endDate',
            'statutoryName', 'website',
            'email', 'phone', 'address', 'calendarId'
        ];
        if ($userCanEdit) {
            $fields[] = 'termEndWarningDays';
            $fields[] = 'applicationLifetime';
        }
        $address = $address ? "<address>$address\n$city $state $zip</address>" : null;

        $endDate = $this->committee->getEndDate(DATE_FORMAT);

        foreach ($fields as $f) {
            if ($$f) {
                if ($f === 'termEndWarningDays') {
                    $$f = $$f.' '.$this->_(['day', 'days', $$f]);
                }
                echo "
                <dl>
                    <dt>{$this->_($f)}</dt>
                    <dd>{$$f}</dd>
                </dl>
                ";
            }
        }
    ?>
    <?= $description; ?>

    <h2><?= $this->_('meetingSchedule'); ?></h2>
    <?= $meetingSchedule; ?>
    </div>
</section>
