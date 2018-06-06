<?php
/**
 * @copyright 2016-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Blossom\Classes\Url;

$helper = $this->template->getHelper('buttonLink');
$a = '<a href="%s"%s>%s</a>';


$current_url = new Url(Url::current_url(BASE_HOST));

$current = $current_url->path == BASE_URI.'/seats';
$seatsLink = sprintf($a,
    BASE_URI.'/seats',
    $current ? ' class="current"' : '',
    $this->_(['seat', 'seats', 2])
);

$current = $current_url->path == BASE_URI.'/seats/vacancies';
$vacanciesLink = sprintf($a,
    BASE_URI.'/seats/vacancies',
    $current ? ' class="current"' : '',
    $this->_(['vacancy', 'vacancies', 2])
);
?>
<section>
    <header id="seatsHeader">
        <nav>
        <?= "$seatsLink $vacanciesLink"; ?>
        </nav>
    </header>
</section>
