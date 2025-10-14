<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Members\Reappoint;

use Application\Models\Member;

class View extends \Web\View
{
    public function __construct(Member $member)
    {
        parent::__construct();

        $this->vars = [
            'member'        => $member,
            'seat'          => $member->getSeat(),
            'term'          => $member->getTerm(),
            'committee'     => $member->getCommittee()
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/members/reappointForm.twig', $this->vars);
    }
}
