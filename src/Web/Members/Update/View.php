<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Members\Update;

use Application\Models\Member;
use Application\Models\Person;

class View extends \Web\View
{
    public function __construct(Member $member)
    {
        parent::__construct();

        $return_url = $member->getSeat_id()
                    ? parent::generateUri('seats.view')."?seat_id=".$member->getSeat_id()
                    : parent::generateUri('committees.members')."?committee_id=".$member->getCommittee_id();

        $person = $member->getPerson() ?? new Person();

        $this->vars = [
            'member'     => $member,
            'committee'  => $member->getCommittee(),
            'person'     => $person,
            'return_url' => $return_url
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/members/updateForm.twig', $this->vars);
    }
}
