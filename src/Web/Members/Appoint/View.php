<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Members\Appoint;

use Application\Models\Member;
use Application\Models\Seat;

class View extends \Web\View
{
    public function __construct(Member $newMember, Member $currentMember=null)
    {
        parent::__construct();

        $seat = $newMember->getSeat();

        $this->vars = [
            'committee'     => $newMember->getCommittee(),
            'newMember'     => $newMember,
            'currentMember' => $currentMember,
            'requirements'  => $seat ? $seat->getRequirements()   : null,
            'recentMembers' => $seat ? self::recentMembers($seat) : null,
            'termOptions'   => $seat ? self::termOptions  ($seat) : null
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/members/appointForm.twig', $this->vars);
    }

    public static function recentMembers(Seat $seat): array
    {
        $out        = [];
        $termLength = new \DateInterval($seat->getTermLength());

        $currentTerm = $seat->getTerm(time());
        if ($currentTerm) {
            $nextTerm = $currentTerm->getNextTerm();

            $list = [$nextTerm, $currentTerm];

            $d = new \DateTime('now');
            $d->sub($termLength);
            $previousTerm = $seat->getTerm((int)$d->format('U'));
            if ($previousTerm) {
                $list[] = $previousTerm;
            }

            foreach ($list as $term) {
                if (count($term->getMembers()) > 0 ) {
                    $members = [];
                    foreach ($term->getMembers() as $m) {
                        $members[] = $m->getPerson()->getFullname();
                    }
                    $members = implode(', ', $members);
                    $out[] = [
                        'startDate' => $term->getStartDate(DATE_FORMAT),
                          'endDate' => $term->getEndDate(DATE_FORMAT),
                          'members' => $members
                    ];
                }
            }
        }
        return $out;
    }

    private static function termOptions(Seat $seat): array
    {
        if ($seat->getType() != 'termed') { return []; }

        $out = [];
        $termLength = new \DateInterval($seat->getTermLength());

        $currentTerm = $seat->getTerm(time());
        if ($currentTerm) {
            if ($currentTerm->isVacant()) { $out[] = $currentTerm; }

            $defaultTermId = $currentTerm->getId();

            $d = new \DateTime('now');
            $d->add($termLength);
            $nextTerm = $seat->getTerm((int)$d->format('U'));
            if ($nextTerm->isVacant()) { $out[] = $nextTerm; }

            $list = [$nextTerm, $currentTerm];

            $d = new \DateTime('now');
            $d->sub($termLength);
            $previousTerm = $seat->getTerm((int)$d->format('U'));
            if ($previousTerm && $previousTerm->isVacant()) { $out[] = $previousTerm; }
        }
        return $out;
    }
}
