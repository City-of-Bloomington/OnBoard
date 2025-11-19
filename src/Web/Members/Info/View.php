<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Members\Info;

use Application\Models\Member;
use Application\Models\Seat;

class View extends \Web\View
{
    public function __construct(Member $member)
    {
        parent::__construct();

        $this->vars = [
            'member'        => $member,
            'committee'     => $member->getCommittee(),
            'seat'          => $member->getSeat(),
            'term'          => $member->getTerm(),
            'actionLinks'   => self::actionLinks($member),
            'termIntervals' => Seat::$termIntervals,
            'termModifiers' => Seat::$termModifiers,
            'breadcrumbs'   => self::breadcrumbs($member)
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/members/info.twig', $this->vars);
    }

    private static function breadcrumbs(Member $m): array
    {
        $committee_id = $m->getCommittee_id();
        $committee    = $m->getCommittee()->getName();
        $members      = parent::_(['member', 'members', 10]);
        return [
            $committee  => parent::generateUri('committees.info',    ['committee_id'=>$committee_id]),
            $members    => parent::generateUri('committees.members', ['committee_id'=>$committee_id]),
            $m->getPerson()->getFullname() => null
        ];
    }

    private static function actionLinks(Member $m): array
    {
        $links = [];
        if (parent::isAllowed('members', 'update')) {
            $links[] = [
                'url'   => parent::generateUri('members.update', ['member_id'=>$m->getId()]),
                'label' => parent::_('member_edit'),
                'class' => 'edit'
            ];
        }
        if (parent::isAllowed('members', 'resign')
            && (!$m->getEndDate() || strtotime($m->getEndDate()) > time() )) {
            $links[] = [
                'url'   => parent::generateUri('members.resign', ['member_id'=>$m->getId()]),
                'label' => parent::_('member_end'),
                'class' => 'close'
            ];
        }
        $seat = $m->getSeat();
        if (parent::isAllowed('members', 'reappoint')
            && $seat && $seat->getType() == 'termed'
            && $m->getTerm()->getNextTerm()->isVacant()) {

            $links[] = [
                'url'   => parent::generateUri('members.reappoint', ['member_id'=>$m->getId()]),
                'label' => parent::_('member_continue'),
                'class' => 'loop'
            ];
        }
        if (parent::isAllowed('members', 'delete')) {
            $links[] = [
                'url'   => parent::generateUri('members.delete', ['member_id'=>$m->getId()]),
                'label' => parent::_('member_delete'),
                'class' => 'delete'
            ];
        }
        if (parent::isAllowed('offices', 'update')) {
            $links[] = [
                'url'   => parent::generateUri('offices.add')."?committee_id={$m->getCommittee_id()};person_id={$m->getPerson_id()}",
                'label' => parent::_('office_add'),
                'class' => 'add'
            ];
            foreach ($m->getPerson()->getOffices($m->getCommittee(), date('Y-m-d')) as $office) {
                $links[] = [
                    'url'   => parent::generateUri('offices.update', ['office_id' => $office->getId()]),
                    'label' => sprintf(parent::_('office_edit', 'messages'),  $office->getTitle()),
                    'class' => 'edit'
                ];
            }
        }

        return $links;
    }
}
