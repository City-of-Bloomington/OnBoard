<?php
/**
 * @copyright 2009-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @param Committee $this->committee
 */
use Application\Models\Person;
use Blossom\Classes\Url;

$committee_id = $this->committee->getId();

$helper = $this->template->getHelper('buttonLink');
$a = '<a href="%s" id="%s"%s>%s</a>';
$current_url = new Url(Url::current_url(BASE_HOST));

$current = $current_url->path == BASE_URI.'/committees/info';
$infoLink = sprintf($a,
    BASE_URI."/committees/info?committee_id=$committee_id",
    'committee_info',
    $current ? ' class="current"' : '',
    $this->_('committee_info')
);



$current = $current_url->path == BASE_URI.'/committees/members'
        && $current_url->current != '0';
$membersLink = sprintf($a,
    BASE_URI."/committees/members?committee_id=$committee_id",
    'current_members',
    $current ? ' class="current"' : '',
    $this->_(['current_member', 'current_members', 2])
);


$current = strpos($current_url->path, BASE_URI.'/legislation') !== false;
$legislationLink = '';
if ($this->committee->isLegislative()) {
    $legislationLink = sprintf($a,
        BASE_URI."/legislation?committee_id=$committee_id;year=".date('Y'),
        'legislation',
        $current ? ' class="current"' : '',
        $this->_(['legislation', 'legislations', 10])
    );
}

$current = strpos($current_url->path, BASE_URI.'/reports') !== false;
$reportsLink = '';
$reportsLink = sprintf($a,
    BASE_URI."/reports?committee_id=$committee_id",
    'reports',
    $current ? ' class="current"' : '',
    $this->_(['report', 'reports', 10])
);

$current = $current_url->path == BASE_URI.'/meetingFiles';
$filesLink = sprintf($a,
    BASE_URI."/meetingFiles?committee_id=$committee_id",
    'meetingFiles',
    $current ? ' class="current"' : '',
    $this->_(['meetingFile', 'meetingFiles', 10])
);

$pastSeatsLink = '';
if ($this->committee->hasPastSeats()) {
    $current = $current_url->path == BASE_URI.'/committees/seats'
            && $current_url->current != '0';
    $pastSeatsLink = sprintf($a,
        BASE_URI."/committees/seats?committee_id=$committee_id;current=0",
        'seats_past',
        $current ? ' class="current"' : '',
        $this->_('seats_past')
    );
}

$pastMembersLink = '';
if ($this->committee->getType() === 'open' &&  $this->committee->hasPastMembers()) {
    $current = $current_url->path == BASE_URI.'/committees/members'
            && $current_url->current != '0';
    $pastMembersLink = sprintf($a,
        BASE_URI."/committees/members?committee_id=$committee_id;current=0",
        'past_members',
        $current ? ' class="current"' : '',
        $this->_(['past_member', 'past_members', 2])
    );
}

$applicationsLink = '';
if (Person::isAllowed('committees', 'applications') && $this->committee->takesApplications()) {
    $current = $current_url->path == BASE_URI.'/committees/applications';
    $applicationsLink = sprintf($a,
        BASE_URI."/committees/applications?committee_id=$committee_id",
        'applications',
        $current ? ' class="current"' : '',
        $this->_(['application', 'applications', 2])
    );
}

$historyLink = '';
if (Person::isAllowed('committees', 'history')) {
    $current = $current_url->path == BASE_URI.'/committees/history';
    $historyLink = sprintf($a,
        BASE_URI."/committees/history?committee_id=$committee_id",
        'committeeHistory',
        $current ? ' class="current"' : '',
        $this->_('committeeHistory')
    );
}

echo "
<header id=\"committeeHeader\">
";
    $this->_include('committees/partials/committeeVacancyHeading.inc');
echo "
    <nav>
        $infoLink
        $membersLink
        $pastSeatsLink
        $pastMembersLink
        $applicationsLink
        $filesLink
        $legislationLink
        $reportsLink
        $historyLink
    </nav>
</header>
";
