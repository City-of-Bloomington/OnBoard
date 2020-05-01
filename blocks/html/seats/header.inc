<?php
/**
 * @copyright 2016-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
use Web\Url;

$helper = $this->template->getHelper('buttonLink');
$a = '<a href="%s"%s>%s</a>';


$current_url = new Url(Url::current_url(BASE_HOST));

// Seats
$uri       = parent::generateUri('seats.index');
$current   = $current_url->path == $uri;
$seatsLink = sprintf($a, $uri,
                         $current ? ' class="current"' : '',
                         $this->_(['seat', 'seats', 2]));

// Vacancies
$uri           = parent::generateUri('seats.vacancies');
$current       = $current_url->path == $uri;
$vacanciesLink = sprintf($a, $uri,
                             $current ? ' class="current"' : '',
                             $this->_(['vacancy', 'vacancies', 2]));
?>
<section>
    <header id="seatsHeader">
        <nav>
        <?= "$seatsLink $vacanciesLink"; ?>
        </nav>
    </header>
</section>
