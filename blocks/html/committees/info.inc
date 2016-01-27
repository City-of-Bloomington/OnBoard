<?php
/**
 * @copyright 2014-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Committee $this->committee
 */
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;
use Blossom\Classes\View;
use Michelf\MarkdownExtra;

$fields = [
    'type',
    'name', 'statutoryName', 'statuteReference', 'statuteUrl', 'yearFormed', 'website',
    'email', 'phone', 'address', 'city', 'state', 'zip', 'termEndWarningDays'
];
foreach ($fields as $f) {
    $get = 'get'.ucfirst($f);
    $$f = View::escape($this->committee->$get());
}

if ($statuteUrl) {
    $statuteReference = "<a href=\"$statuteUrl\">$statuteReference</a>";
}
if ($website) {
    $website = "<a href=\"$website\">$website</a>";
}

$markdown = new MarkdownExtra();
$description     = $markdown->transform($this->committee->getDescription());
$contactInfo     = $markdown->transform($this->committee->getContactInfo());
$meetingSchedule = $markdown->transform($this->committee->getMeetingSchedule());

$editButton = '';
if (Person::isAllowed('committees', 'edit')) {
    $helper = $this->template->getHelper('buttonLink');
    $editButton = $helper->buttonLink(
        BASE_URI.'/committees/update?committee_id='.$this->committee->getId(),
        $this->_('edit'),
        'edit',
        ButtonLink::SIZE_ICON
    );
}
?>
<section     class="fn1-uiBlock">
    <header  class="fn1-uiBlock-header">
        <h1  class="fn1-uiBlock-title"><?= $this->_('committee_info') ?></h1>
        <div class="fn1-uiBlock-headerTools"><?= $editButton ?></div>
    </header>
    <div class="fn1-uiBlock-content">
    <?php
        $fields  = [
            'type', 'yearFormed',
            'statutoryName', 'statuteReference', 'website',
            'email', 'phone', 'termEndWarningDays', 'address'
        ];
        $address = $address ? "<address>$address\n$city $state $zip</address>" : null;

        foreach ($fields as $f) {
            if ($$f) {
                if ($f === 'termEndWarningDays') {
                    $$f = $$f.' '.$this->_(['day', 'days', $$f]);
                }
                echo "
                <dl class=\"fn1-output-field\">
                    <dt>{$this->_($f)}</dt>
                    <dd>{$$f}</dd>
                </dl>
                ";
            }
        }
    ?>
    <?= $description ?>

    <h2><?= $this->_('meetingSchedule') ?></h2>
    <?= $meetingSchedule ?>

    <h2><?= $this->_('contactInfo') ?></h2>
    <?= $contactInfo ?>

    </div>
</section>
