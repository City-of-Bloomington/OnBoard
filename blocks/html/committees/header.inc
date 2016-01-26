<?php
/**
 * @copyright 2009-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @param Committee $this->committee
 */
use Application\Models\Person;

$helper = $this->template->getHelper('buttonLink');
$infoLink = $helper->buttonLink(
    BASE_URI.'/committees/info?committee_id='.$this->committee->getId(),
    $this->_('committee_info')
);
$membersLink = $helper->buttonLink(
    BASE_URI.'/committees/members?committee_id='.$this->committee->getId(),
    $this->_(['member', 'members', 2])
);

$c = $this->committee->getVacancyCount();
$vacancies = $c ? "($c {$this->_(['vacancy', 'vacancies', $c])})" : '';
$name = self::escape($this->committee->getName());
?>
<section>
    <h1><?= $name; ?> <?= $vacancies; ?></h1>
    <nav class="fn1-btn-container">
        <?= $infoLink; ?>
        <?= $membersLink; ?>
    </nav>
</section>
