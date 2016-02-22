<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Application\Models\Person;

$links = [];

$routes = [
    'applicants' => 'applicant',
    'departments'=> 'department',
    'appointers' => 'appointer',
    'races'      => 'race',
    'users'      => 'user'
];
if (defined('VOTE_TRACKING') && VOTE_TRACKING) {
    $routes['tags'] = 'tag';
}

foreach ($routes as $plural=>$singular) {
    $requiredAction = 'index';
    if (Person::isAllowed($plural, $requiredAction)) {
        $links[] = ['url'=>BASE_URI."/$plural", 'label'=>$this->_([$singular, $plural, 2])];
    }
}
$links[] = ['url'=>BASE_URI.'/site', 'label'=>$this->_('siteContent')];

if ($links) {
    $helper = $this->getHelper('dropdown');
    echo $helper->dropdown($links, $this->_('admin'), 'fn1-siteMenu');
}
