<?php
/**
 * @copyright 2016-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/agpl.html GNU/AGPL, see LICENSE
 * @param Seat $this->seat
 */
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;
use Web\Block;

$termLength = new \DateInterval($this->seat->getTermLength());

$currentTerm = $this->seat->getTerm(time());
if ($currentTerm) {
    $nextTerm = $currentTerm->getNextTerm();

    $list = [$nextTerm, $currentTerm];

    $d = new \DateTime('now');
    $d->sub($termLength);
    $previousTerm = $this->seat->getTerm((int)$d->format('U'));
    if ($previousTerm) {
        $list[] = $previousTerm;
    }

    echo '<div>';
    echo "<h2>{$this->_(['recent_member', 'recent_members', count($list)])}</h2>";
    foreach ($list as $term) {
        if (count($term->getMembers()) > 0 ) {
            $members = [];
            foreach ($term->getMembers() as $m) {
                $members[] = $m->getPerson()->getFullname();
            }
            $members = implode(', ', $members);

            echo "
                <div>
                    $members
                    <span class=\"termDates\">
                        {$term->getStartDate(DATE_FORMAT)}-{$term->getEndDate(DATE_FORMAT)}
                    </span>
                </div>
            ";
        }
    }
    echo '</div>';
}
