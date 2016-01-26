<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
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
    <nav class="fn1-btn-container">
    <?php echo "$seatsLink $vacanciesLink"; ?>
    </nav>
</section>