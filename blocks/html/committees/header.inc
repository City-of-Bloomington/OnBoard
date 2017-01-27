<?php
/**
 * @copyright 2009-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @param Committee $this->committee
 */
use Application\Models\Person;
$committee_id = $this->committee->getId();

$helper = $this->template->getHelper('buttonLink');

$infoLink = $helper->buttonLink(
    BASE_URI."/committees/info?committee_id=$committee_id",
    $this->_('committee_info')
);
$membersLink = $helper->buttonLink(
    BASE_URI."/committees/members?committee_id=$committee_id",
    $this->_(['current_member', 'current_members', 2])
);
$meetingsLink = $helper->buttonLink(
    BASE_URI."/committees/meetings?committee_id=$committee_id",
    $this->_(['meeting', 'meetings', 10])
);
$mediaLink = $helper->buttonLink(
    BASE_URI."/meetingMedia?committee_id=$committee_id",
    $this->_(['meetingMedia', 'meetingMedias', 10])
);

$pastSeatsLink = '';
if ($this->committee->hasPastSeats()) {
    $pastSeatsLink = $helper->buttonLink(
        BASE_URI."/committees/seats?committee_id=$committee_id;current=0",
        $this->_('seats_past')
    );
}

$pastMembersLink = '';
if ($this->committee->getType() === 'open' &&  $this->committee->hasPastMembers()) {
    $pastMembersLink = $helper->buttonLink(
        BASE_URI."/committees/members?committee_id=$committee_id;current=0",
        $this->_(['past_member', 'past_members', 2])
    );
}

$applicationsLink = '';
if (Person::isAllowed('committees', 'applications')) {
    $applications = $this->committee->getApplications(['current'=>time()]);
    $c = count($applications);
    if ($c) {
        $applicationsLink = $helper->buttonLink(
            BASE_URI."/committees/applications?committee_id=$committee_id",
            $this->_(['application', 'applications', 2])
        );
    }
}

echo "
<section>
    <header>
";
        $this->_include('committees/partials/committeeVacancyHeading.inc');
echo"
        <nav class=\"tools\">
            $infoLink
            $membersLink
            $pastSeatsLink
            $pastMembersLink
            $applicationsLink
            $meetingsLink
            $mediaLink
        </nav>
    </header>
</section>
";
