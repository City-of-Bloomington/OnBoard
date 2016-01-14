<?php
/**
 * @copyright 2009-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Committee $this->committee (optional)
 */
use Application\Models\Person;

if (isset($this->committee)) {
    $info = BASE_URI.'/committees/info?committee_id='.$this->committee->getId();
    $name = self::escape($this->committee->getName());
    $members = BASE_URI.'/committees/members?committee_id='.$this->committee->getId();
    $seats   = BASE_URI.'/committees/seats?committee_id='.$this->committee->getId();

    $c = $this->committee->getVacancyCount();
    $vacancies = $c ? "($c {$this->_(['vacancy', 'vacancies', $c])})" : '';

    echo "
    <section>
        <h1>$name $vacancies</h1>
        <nav class=\"fn1-btn-container\">
    ";
        if (Person::isAllowed('committees', 'members')) {
            echo "<a href=\"$members\" class=\"fn1-btn\">{$this->_(['member', 'members', 2])}</a>";
        }
        if (Person::isAllowed('committees', 'info')) {
            echo "<a href=\"$info\" class=\"fn1-btn\">{$this->_('committee_info')}</a>";
        }
        if (Person::isAllowed('committees', 'seats')) {
            echo "<a href=\"$seats\" class=\"fn1-btn\">{$this->_(['seat', 'seats', 2])}</a>";
        }
    echo "
        </nav>
    </section>
    ";
}
