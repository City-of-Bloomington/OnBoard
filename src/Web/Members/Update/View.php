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
    public function __construct(Member $m)
    {
        parent::__construct();

        $url = $m->getSeat_id()
                ? parent::generateUri(     'seats.view'   , ['id'=>$m->getSeat_id()      ])
                : parent::generateUri('committees.members', ['id'=>$m->getCommittee_id() ]);

        $person = $m->getPerson() ?? new Person();

        $this->vars = [
            'member'     => $m,
            'committee'  => $m->getCommittee(),
            'person'     => $person,
            'return_url' => $url
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/members/updateForm.twig', $this->vars);
    }
}
