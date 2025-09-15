<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Members\Update;

use Application\Models\Member;
use Application\Models\Person;

class View extends \Web\View
{
    public function __construct(Member $m, string $return_url)
    {
        parent::__construct();

        $this->vars = [
            'member'      => $m,
            'committee'   => $m->getCommittee(),
            'return_url'  => $return_url,
            'breadcrumbs' => self::breadcrumbs($m)
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/members/updateForm.twig', $this->vars);
    }

    private static function breadcrumbs(Member $m): array
    {
        $committee_id = $m->getCommittee_id();
        $committee    = $m->getCommittee()->getName();
        $members      = parent::_(['member', 'members', 10]);
        $member       = $m->getPerson()->getFullname();

        $out = [
            $committee  => parent::generateUri('committees.info',    ['committee_id'=>$committee_id]),
            $members    => parent::generateUri('committees.members', ['committee_id'=>$committee_id]),
        ];
        if ($m->getCommittee()->getType() == 'seated') {
            $out[$member] = parent::generateUri('members.view', ['member_id'=>$m->getId()]);
        }
        $out[parent::_('member_edit')] = null;
        return $out;
    }
}
