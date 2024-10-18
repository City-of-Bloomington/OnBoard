<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Alternates\Update;

use Application\Models\Alternate;

class View extends \Web\View
{
    public function __construct(Alternate $a)
    {
        parent::__construct();

        $url = $a->getSeat_id()
                ? parent::generateUri('seats.view').'?seat_id='.$a->getSeat_id()
                : parent::generateUri('committees.members', ['id'=>$a->getCommittee_id()]);

        $this->vars = [
            'alternate'  => $a,
            'committee'  => $a->getCommittee(),
            'seat'       => $a->getSeat(),
            'term'       => $a->getTerm(),
            'return_url' => $url
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/alternates/updateForm.twig', $this->vars);
    }
}
