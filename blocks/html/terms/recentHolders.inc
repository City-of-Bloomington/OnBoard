<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/agpl.html GNU/AGPL, see LICENSE.txt
 * @author Dan Hiester <hiesterd@bloomington.in.gov>
 * @param Seat $this->seat
 */
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;
use Blossom\Classes\Block;

$termLength = new \DateInterval($this->seat->getTermLength());

$currentTerm = $this->seat->getTerm(time());
if ($currentTerm) {
    $d = new \DateTime();
    $d->add($termLength);
    $nextTerm = $this->seat->getTerm($d->format('U'));

    $list = [$nextTerm, $currentTerm];

    $d = new \DateTime();
    $d->sub($termLength);
    $previousTerm = $this->seat->getTerm($d->format('U'));
    if ($previousTerm) {
        $list[] = $previousTerm;
    }

    echo '<section>';
    foreach ($list as $term) {
        $members = [];
        foreach ($term->getMembers() as $m) {
            $members[] = $m->getPerson()->getFullname();
        }
        $members = implode(', ', $members);

        echo "
        <div>
            <span class=\"onboard-dates\">
                {$term->getStartDate(DATE_FORMAT)}-{$term->getEndDate(DATE_FORMAT)}
            </span>
            $members
        </div>
        ";
    }
    echo '</section>';
}
