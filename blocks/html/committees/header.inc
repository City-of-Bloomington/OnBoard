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
    $this->_(['member', 'members', 2])
);

$applicationsLink = '';
if (Person::isAllowed('committees', 'applications')) {
    $applications = $this->committee->getApplications(['current'=>time()]);
    $c = count($applications);
    if ($c) {
        $applicationsLink = $helper->buttonLink(
            BASE_URI."/committees/applications?committee_id=$committee_id",
            $this->_(['application', 'applications', $c])
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
            $applicationsLink
        </nav>
    </header>
</section>
";
