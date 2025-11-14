<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Seats\View;

use Application\Models\Alternate;
use Application\Models\Member;
use Application\Models\Seat;
use Application\Models\Term;

class View extends \Web\View
{
    public function __construct(Seat $seat)
    {
        parent::__construct();

        $this->vars = [
            'seat'          => $seat,
            'committee'     => $seat->getCommittee(),
            'seatActions'   => self::actionLinksForSeat($seat),
            'termActions'   => [],
            'termIntervals' => Seat::$termIntervals,
            'termModifiers' => Seat::$termModifiers,
            'breadcrumbs'   => self::breadcrumbs($seat)
        ];

        if ($seat->getType() == 'termed') {
            $this->vars['terms'] = $this->term_data($seat);
            if (parent::isAllowed('terms', 'generate')) {
                if (isset($this->vars['terms'][0])) {
                    $l    = count($this->vars['terms']) - 1;
                    $next = parent::generateUri('terms.generate', ['term_id'=>$this->vars['terms'][0 ]['term_id']]).'?direction=next';
                    $prev = parent::generateUri('terms.generate', ['term_id'=>$this->vars['terms'][$l]['term_id']]).'?direction=previous';


                    $this->vars['termActions'][] = [
                        'url'   => $next,
                        'label' => parent::_('term_add_next'),
                        'class' => 'add'
                    ];
                    $this->vars['termActions'][] = [
                        'url'   => $prev,
                        'label' => parent::_('term_add_previous'),
                        'class' => 'add'
                    ];
                }
                else {
                    $this->vars['termActions'][] = [
                        'url'   => parent::generateUri('terms.add')."?seat_id={$seat->getId()}",
                        'label' => parent::_('term_add'),
                        'class' => 'add'
                    ];
                }
            }
        }
        else {
            $this->vars['members'] = $this->member_data($seat);

            if (parent::isAllowed('members', 'appoint')) {
                $this->vars['memberActions'] = [[
                    'url'   => parent::generateUri('members.appoint')."?seat_id={$seat->getId()}",
                    'label' => parent::_('member_add'),
                    'class' => 'add'
                ]];
            }
        }
    }

    public function render(): string
    {
        return $this->twig->render('html/seats/info.twig', $this->vars);
    }

    private static function breadcrumbs(Seat $s)
    {
        $committee_id = $s->getCommittee_id();
        $committee    = $s->getCommittee()->getName();
        $seats        = parent::_(['seat', 'seats', 10]);
        $seat         = "{$s->getCode()} {$s->getName()}";
        return [
            $committee  => parent::generateUri('committees.info',  ['committee_id'=>$committee_id]),
            $seats      => parent::generateUri('committees.seats', ['committee_id'=>$committee_id]),
            $seat       => null
        ];
    }

    private function actionLinksForSeat(Seat $seat): array
    {
        $links   = [];
        $seat_id = $seat->getId();
        if (parent::isAllowed('seats', 'update')) {
            $links[] = [
                'url'   => parent::generateUri('seats.update', ['seat_id'=>$seat_id]),
                'label' => parent::_('seat_edit'),
                'class' => 'edit'
            ];
        }
        if (parent::isAllowed('seats', 'delete') && $seat->isSafeToDelete()) {
            $links[] = [
                'url'   => parent::generateUri('seats.delete', ['seat_id'=>$seat_id]),
                'label' => parent::_('seat_delete'),
                'class' => 'delete'
            ];
        }
        $endDate = $seat->getEndDate('U');
        if (parent::isAllowed('seats', 'end') && (!$endDate || $endDate > time())) {
            $links[] = [
                'url'   => parent::generateUri('seats.end', ['seat_id'=>$seat_id]),
                'label' => parent::_('seat_end')
            ];
        }
        $links[] = [
            'url'   => parent::generateUri('seats.view', ['seat_id'=>$seat->getId()]).'?format=csv',
            'label' => 'CSV',
            'class' => 'download'
        ];
        return $links;
    }

    private function actionLinksForTerm(Term $term, string $return_url): array
    {
        $links      = [];
        $term_id    = $term->getId();

        if (parent::isAllowed('terms', 'update')) {
            $links[] = [
                'url'   => parent::generateUri('terms.update', ['term_id'=>$term_id]),
                'label' => parent::_('term_edit'),
                'class' => 'edit'
            ];
        }

        if (parent::isAllowed('terms', 'delete') && $term->isSafeToDelete()) {
            $links[] = [
                'url'   => parent::generateUri('terms.delete', ['term_id'=>$term_id]),
                'label' => parent::_('term_delete'),
                'class' => 'delete'
            ];
        }
        if (parent::isAllowed('members', 'appoint')) {
            $links[] = [
                'url'   => parent::generateUri('members.appoint')."?term_id=$term_id;return_url=$return_url",
                'label' => parent::_('member_add'),
                'class' => 'add'
            ];
        }
        if (parent::isAllowed('alternates', 'add') && $this->vars['committee']->allowsAlternates()) {
            $p = ['term_id'=>$term_id, 'return_url'=>$return_url];

            $links[] = [
                'url'   => parent::generateUri('alternates.add').'?'.http_build_query($p, '', ';'),
                'label' => parent::_('alternate_add'),
                'class' => 'add'
            ];
        }
        return $links;
    }

    private function actionLinksForMember(Member $m): array
    {
        $links = [];
        if (parent::isAllowed('members', 'update')) {
            $links[] = [
                'url'   => parent::generateUri('members.update', ['member_id'=>$m->getId()]),
                'label' => $this->_('member_edit'),
                'class' => 'edit'
            ];
        }
        if (parent::isAllowed('members', 'delete')) {
            $links[] = [
                'url'   => parent::generateUri('members.delete', ['member_id'=>$m->getId()]),
                'label' => $this->_('member_delete'),
                'class' => 'delete'
            ];
        }
        if (parent::isAllowed('offices', 'update')) {
            $links[] = [
                'url'   => parent::generateUri('offices.add')."?committee_id={$m->getCommittee_id()};person_id={$m->getPerson_id()}",
                'label' => $this->_('office_add'),
                'class' => 'add'
            ];
            foreach ($m->getPerson()->getOffices($m->getCommittee(), date('Y-m-d')) as $office) {
                $links[] = [
                    'url'   => parent::generateUri('offices.update', ['office_id'=>$office->getId()]),
                    'label' => sprintf($this->_('office_edit', 'messages'), $office->getTitle()),
                    'class' => 'edit'
                ];
            }
        }

        return $links;
    }

    private function actionLinksForAlternate(Alternate $a): array
    {
        $links = [];
        if (parent::isAllowed('alternates', 'update')) {
            $links[] = [
                'url'   => parent::generateUri('alternates.update', ['alternate_id'=>$a->getId()]),
                'label' => parent::_('alternate_edit'),
                'class' => 'edit'
            ];
        }
        if (parent::isAllowed('alternates', 'delete')) {
            $links[] = [
                'url'   => parent::generateUri('alternates.delete', ['alternate_id'=>$a->getId()]),
                'label' => parent::_('alternate_delete'),
                'class' => 'delete'
            ];
        }
        return $links;
    }


    private function term_data(Seat $seat): array
    {
        $today = date('Y-m-d');
        $terms = [];
        $ret   = parent::generateUrl('seats.view', ['seat_id'=>$seat->getId()]);
        foreach ($seat->getTerms() as $t) {
            $terms[] = [
                'term_id'     => $t->getId(),
                'startDate'   => $t->getStartDate(),
                'endDate'     => $t->getEndDate(),
                'current'     => $t->getStartDate() < $today && $today < $t->getEndDate(),
                'actionLinks' => $this->actionLinksForTerm($t, $ret),
                'members'     => $this->member_data($t),
                'alternates'  => $this->alternate_data($t)
            ];
        }
        return $terms;
    }

    private function member_data(Seat|Term $t): array
    {
        $members = [];

        foreach ($t->getMembers() as $m) {
            $titles  = [];
            $offices = $m->getPerson()->getOffices($m->getCommittee(), date('Y-m-d'));
            foreach ($offices as $o) { $titles[] = $o->getTitle(); }

            $members[] = [
                'member_id'   => $m->getId(),
                'person_id'   => $m->getPerson_id(),
                'name'        => $m->getPerson()->getFullname(),
                'startDate'   => $m->getStartDate(),
                'endDate'     => $m->getEndDate(),
                'titles'      => implode(', ', $titles),
                'actionLinks' => $this->actionLinksForMember($m)
            ];
        }
        return $members;
    }

    private function alternate_data(Term $term): array
    {
        $alternates = [];
        foreach ($term->getAlternates() as $a) {
            $alternates[] = [
                'alternate_id' => $a->getId(),
                'person_id'    => $a->getPerson_id(),
                'name'         => $a->getPerson()->getFullname(),
                'startDate'    => $a->getStartDate(),
                'endDate'      => $a->getEndDate(),
                'actionLinks'  => $this->actionLinksForAlternate($a)
            ];
        }
        return $alternates;
    }
}
