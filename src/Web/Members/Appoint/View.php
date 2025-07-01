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
    public function __construct(Member $member, string $return_url)
    {
        parent::__construct();

        $seat = $member->getSeat();

        $this->vars = [
            'committee'     => $member->getCommittee(),
            'member'        => $member,
            'requirements'  => $seat ? $seat->getRequirements()   : null,
            'return_url'    => $return_url
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/members/updateForm.twig', $this->vars);
    }
}
