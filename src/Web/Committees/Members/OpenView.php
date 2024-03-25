<?php
/**
 * Committee members for committees with open membership
 *
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Committees\Members;

use Application\Models\Committee;
use Web\View;

class OpenView extends View
{
    public function __construct(Committee $committee, array $members, bool $current)
    {
        parent::__construct();

        $title = $current
                 ? parent::_(['current_member', 'current_members', count($members)])
                 : parent::_(['past_member',    'past_members',    count($members)]);

        if (isset($_SESSION['USER'])) {
            $this->createActionLinks($committee, $seat_data);
        }

        $this->vars = [
            'committee' => $committee,
            'members'   => $members,
            'title'     => $title
        ];
    }

    public function render(): string
    {
        return $this->twig->render("{$this->outputFormat}/committees/open_members.twig", $this->vars);
    }

    private function createActionLinks(Committee $committee, array &$members)
    {
        $committee_id         = $committee->getId();
        $userCanEditOffices   = parent::isAllowed('offices', 'update');
        $userCanEditMembers   = parent::isAllowed('members', 'update');
        $userCanDeleteMembers = parent::isAllowed('members', 'delete');

        foreach ($members as $i=>$m) {
            $links   = [];
            $member_id = $m->getId();
            $person_id = $m->getPerson_id();
            $offices   = $m->getPerson()->getOffices($m->getCommittee(), date('Y-m-d'));

            if ($userCanEditOffices) {
                $links[] = [
                    'url'   => parent::generateUri('offices.update')."?committee_id=$committee_id;person_id=$person_id",
                    'label' => $this->_('office_add')
                ];
            }
            foreach ($offices as $o) {
                if ($userCanEditOffices) {
                    $links[] = [
                        'url'   => parent::generateUri('offices.update')."?office_id={$o->getId()}",
                        'label' => sprintf($this->_('office_edit', 'messages'), $o->getTitle())
                    ];
                }
            }

            if ($userCanEditMembers) {
                $links[] = [
                    'url'   => parent::generateUri('members.update')."?member_id=$member_id",
                    'label' => $this->_('member_edit')
                ];
            }
            if ($userCanDeleteMembers) {
                $links[] = [
                    'url'   => parent::generateUri('members.delete')."?member_id=$member_id",
                    'label' => $this->_('member_delete')
                ];
            }

            $members[$i]['actionLinks'] = $links;
        }
    }
}
