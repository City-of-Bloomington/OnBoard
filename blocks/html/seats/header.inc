<?php
/**
 * @copyright 2016-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
$helper = $this->template->getHelper('buttonLink');
$seatsLink = $helper->buttonLink(
    BASE_URI.'/seats',
    $this->_(['seat', 'seats', 2])
);
$vacanciesLink = $helper->buttonLink(
    BASE_URI.'/seats/vacancies',
    $this->_(['vacancy', 'vacancies', 2])
);
?>
<section>
    <header>
        <nav class="tools">
        <?= "$seatsLink $vacanciesLink"; ?>
        </nav>
    </header>
</section>
