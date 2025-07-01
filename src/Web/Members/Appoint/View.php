<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Members\Appoint;

use Application\Models\Member;
use Application\Models\Seat;

class View extends \Web\View
{
    public function __construct(Member $member, Member $currentMember=null)
    {
        parent::__construct();

        $seat = $member->getSeat();

        $this->vars = [
            'committee'     => $member->getCommittee(),
            'member'        => $member,
            'currentMember' => $currentMember,
            'requirements'  => $seat ? $seat->getRequirements()   : null,
            'return_url'    => \Web\View::generateUrl('committees.members', ['committee_id'=>$member->getCommittee_id()])
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/members/updateForm.twig', $this->vars);
    }
}
