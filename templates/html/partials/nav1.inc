<?php
/**
 * @copyright 2009-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Application\Models\Person;
?>
<nav id="nav1">
    <div class="container">
        <?php
            $routes = [
                'committee'   => 'committees',
                'person'      => 'people',
                'seat'        => 'seats',
                'liaison'     => 'liaisons'
            ];
            foreach ($routes as $singular => $plural) {
                if (Person::isAllowed($plural, 'index')) {
                    $uri   = BASE_URI."/$plural";
                    $class = (strpos($_SERVER['REQUEST_URI'], $uri) !== false) ? ' class="current"' : '';
                    echo "<a href=\"$uri\"$class>{$this->_([$singular, $plural, 2])}</a>";
                }
            }


            if (Person::isAllowed('applicants', 'apply')) {
                $uri = BASE_URI.'/applicants/apply';
                $class = (strpos($_SERVER['REQUEST_URI'], $uri) !== false) ? ' class="current"' : '';
                echo "<a href=\"$uri\"$class>{$this->_('apply')}</a>";
            }
        ?>
    </div>
</nav>
