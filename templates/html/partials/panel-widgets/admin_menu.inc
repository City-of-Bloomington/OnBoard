<?php
/**
 * @copyright 2016-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Application\Models\Person;

$links = [];

$routes = [
    'applicants' => 'applicant',
    'departments'=> 'department',
    'appointers' => 'appointer',
    'races'      => 'race',
    'users'      => 'user',
    'tags'       => 'tag',
    'legislationTypes'       => 'legislationType',
    'legislationActionTypes' => 'legislationActionType',
    'legislationStatuses'    => 'legislationStatus'
];

foreach ($routes as $plural=>$singular) {
    $requiredAction = 'index';
    if (Person::isAllowed($plural, $requiredAction)) {
        $links[] = ['url'=>BASE_URI."/$plural", 'label'=>$this->_([$singular, $plural, 2])];
    }
}
if (Person::isAllowed('site', 'siteContent')) {
    $links[] = ['url'=>BASE_URI.'/site', 'label'=>$this->_('siteContent')];
}

if ($links) {
    // TEMPORARY PLACEHOLDER
    // This is a temporary place to put a link to a hastily prepared report
    // for the mayor's office.  We need to rework the navigation to round up
    // all the other reports and give them a real home.
    //
    // https://github.com/City-of-Bloomington/OnBoard/issues/3
    //
    $links[] = ['url'=>BASE_URI.'/committees/report', 'label'=>'Master Report'];

    $helper = $this->getHelper('dropdown');
    echo $helper->dropdown($links, $this->_('admin'), 'admin_menu');
}
