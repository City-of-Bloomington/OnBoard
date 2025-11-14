<?php
/**
 * Committee members for committees with open membership
 *
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Committees\Members;

use Application\Models\Committee;
use Web\View;

class OpenView extends View
{
    public function __construct(Committee $committee, array $members, array $search)
    {
        parent::__construct();

        $this->vars = [
            'committee' => $committee,
            'members'   => $this->member_data($committee, $members),
            'search'    => $search
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/committees/open_members.twig', $this->vars);
    }

    private function member_data(Committee $committee, array &$member_data): array
    {
        $committee_id         = $committee->getId();
        $userCanEditOffices   = parent::isAllowed('offices', 'update');
        $userCanEditMembers   = parent::isAllowed('members', 'update');
        $userCanDeleteMembers = parent::isAllowed('members', 'delete');

        $members = [];
        foreach ($member_data as $m) {
            $links   = [];
            $member_id = $m['member_id'];
            $person_id = $m['member_person_id'];

            if ($userCanEditOffices) {
                $links[] = [
                    'url'   => parent::generateUri('offices.add')."?committee_id=$committee_id;person_id=$person_id",
                    'label' => $this->_('office_add'),
                    'class' => 'add'
                ];
            }
            foreach ($m['offices'] as $o) {
                if ($userCanEditOffices) {
                    $links[] = [
                        'url'   => parent::generateUri('offices.update', ['office_id'=>$o->getId()]),
                        'label' => sprintf($this->_('office_edit', 'messages'), $o->getTitle()),
                        'class' => 'edit'
                    ];
                }
            }

            if ($userCanEditMembers) {
                $links[] = [
                    'url'   => parent::generateUri('members.update', ['member_id'=>$member_id]),
                    'label' => $this->_('member_edit'),
                    'class' => 'edit'
                ];
            }
            if ($userCanDeleteMembers) {
                $links[] = [
                    'url'   => parent::generateUri('members.delete', ['member_id'=>$member_id]),
                    'label' => $this->_('member_delete'),
                    'class' => 'delete'
                ];
            }

            $members[] = [
                'member_id'   => $member_id,
                'person_id'   => $person_id,
                'name'        => "$m[member_firstname] $m[member_lastname]",
                'offices'     => $m['offices'],
                'startDate'   => $m['member_startDate'],
                'endDate'     => $m['member_endDate'],
                'actionLinks' => $links
            ];
        }
        return $members;
    }
}
