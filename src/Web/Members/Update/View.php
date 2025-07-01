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
            'member'     => $m,
            'committee'  => $m->getCommittee(),
            'return_url' => $return_url
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/members/updateForm.twig', $this->vars);
    }
}
