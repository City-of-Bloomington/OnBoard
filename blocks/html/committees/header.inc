<?php
/**
 * @copyright 2009-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE
 * @param Committee $this->committee
 */
use Application\Models\Person;
use Web\Url;

$committee_id = $this->committee->getId();

$helper       = $this->template->getHelper('buttonLink');
$a            = '<a href="%s" id="%s"%s>%s</a>';
$current_url  = new Url(Url::current_url(BASE_HOST));

// Info
$uri      = parent::generateUri('committees.info');
$current  = $current_url->path == $uri;
$infoLink = sprintf($a, "$uri?committee_id=$committee_id",
                        'committee_info',
                        $current ? ' class="current"' : '',
                        $this->_('committee_info'));


// Members
$uri         = parent::generateUri('committees.members');
$current     = $current_url->path == $uri && $current_url->current != '0';
$membersLink = sprintf($a, "$uri?committee_id=$committee_id",
                           'current_members',
                           $current ? ' class="current"' : '',
                           $this->_(['current_member', 'current_members', 2]));


// Legislation
$legislationLink = '';
if ($this->committee->isLegislative()) {
    $uri             = parent::generateUri('legislation.index');
    $current         = strpos($current_url->path, $uri) !== false;
    $legislationLink = sprintf($a, "$uri?committee_id=$committee_id;year=".date('Y'),
                                   'legislation',
                                   $current ? ' class="current"' : '',
                                   $this->_(['legislation', 'legislations', 10]));
}

// Reports
$uri         = parent::generateUri('reports.index');
$current     = strpos($current_url->path, $uri) !== false;
$reportsLink = sprintf($a, "$uri?committee_id=$committee_id",
                           'reports',
                           $current ? ' class="current"' : '',
                           $this->_(['report', 'reports', 10]));

// Meetings
$uri       = parent::generateUri('meetingFiles.index');
$current   = $current_url->path == $uri;
$filesLink = sprintf($a, "$uri?committee_id=$committee_id",
                         'meetingFiles',
                         $current ? ' class="current"' : '',
                         $this->_(['meetingFile', 'meetingFiles', 10]));
// Seats
// Only show the seats link if the user has permission to edit them
$seatsLink = '';
if (Person::isAllowed('seats', 'update') && $this->committee->getType()=='seated') {
    $uri       = parent::generateUri('committees.seats');
    $current   = $current_url->path == $uri;
    $seatsLink = sprintf($a, "$uri?committee_id=$committee_id",
                             'seats',
                             $current ? ' class="current"' : '',
                             $this->_(['seat', 'seats', 10]));
}

// Past Seats
$pastSeatsLink = '';
if ($this->committee->hasPastSeats()) {
    $uri           = parent::generateUri('committees.seats');
    $current       = $current_url->path == $uri && $current_url->current != '0';
    $pastSeatsLink = sprintf($a, "$uri?committee_id=$committee_id;current=0",
                                 'seats_past',
                                 $current ? ' class="current"' : '',
                                 $this->_('seats_past'));
}

// Past Members
$pastMembersLink = '';
if ($this->committee->getType() === 'open' &&  $this->committee->hasPastMembers()) {
    $uri             = parent::generateUri('committees.members');
    $current         = $current_url->path == $uri && $current_url->current != '0';
    $pastMembersLink = sprintf($a, "$uri?committee_id=$committee_id;current=0",
                                   'past_members',
                                   $current ? ' class="current"' : '',
                                   $this->_(['past_member', 'past_members', 2]));
}

// Applications
$applicationsLink = '';
if (Person::isAllowed('committees', 'applications') && $this->committee->takesApplications()) {
    $uri              = parent::generateUri('committees.applications');
    $current          = $current_url->path == $uri;
    $applicationsLink = sprintf($a, "$uri?committee_id=$committee_id",
                                     'applications',
                                     $current ? ' class="current"' : '',
                                     $this->_(['application', 'applications', 2]));
}

// History
$historyLink = '';
if (Person::isAllowed('committees', 'history')) {
    $uri         = parent::generateUri('committees.history');
    $current     = $current_url->path == $uri;
    $historyLink = sprintf($a, "$uri?committee_id=$committee_id",
                               'committeeHistory',
                               $current ? ' class="current"' : '',
                               $this->_('committeeHistory'));
}

echo "
<header id=\"committeeHeader\">
";
    $this->_include('committees/partials/committeeVacancyHeading.inc');
echo "
    <nav>
        $infoLink
        $membersLink
        $seatsLink
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
