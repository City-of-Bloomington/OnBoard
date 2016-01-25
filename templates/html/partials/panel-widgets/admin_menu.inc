<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Application\Models\Person;

$links = '';

$routes = [
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
        $links.= "<a href=\"".BASE_URI."/$plural\" class=\"fn1-dropdown-link\">{$this->_([$singular, $plural, 2])}</a>";
    }
}

if ($links) {
    echo "
    <nav class=\"fn1-dropdown fn1-siteMenu\">
        <button class=\"fn1-dropdown-launcher\" aria-haspopup=\"true\" aria-expanded=\"false\">
            {$this->_('admin')}
        </button>
        <div class=\"fn1-dropdown-links\">
            $links
        </div>
    </nav>
    ";
}
