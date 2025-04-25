<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Members\Info;

use Application\Models\Member;

class View extends \Web\View
{
    public function __construct(Member $member)
    {
        parent::__construct();

        $this->vars = [
            'member'      => $member,
            'committee'   => $member->getCommittee(),
            'actionLinks' => $this->actionLinks($member)
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/members/info.twig', $this->vars);
    }

    private function actionLinks(Member $m): array
    {
        $links = [];
        if (parent::isAllowed('members', 'update')) {
            $links[] = [
                'url'   => parent::generateUri('members.update', ['id'=>$m->getId()]),
                'label' => $this->_('member_edit'),
                'class' => 'edit'
            ];
        }
        if (parent::isAllowed('members', 'delete')) {
            $links[] = [
                'url'   => parent::generateUri('members.delete', ['id'=>$m->getId()]),
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
                    'url'   => parent::generateUri('offices.update', ['id'=>$office->getId()]),
                    'label' => sprintf($this->_('office_edit', 'messages'), $office->getTitle()),
                    'class' => 'edit'
                ];
            }
        }

        return $links;
    }
}
