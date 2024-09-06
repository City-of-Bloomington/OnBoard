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
    public function __construct(Alternate $alternate)
    {
        parent::__construct();

        $return_url = $alternate->getSeat_id()
                    ? parent::generateUri('seats.view').'?seat_id='.$alternate->getSeat_id()
                    : parent::generateUri('committees.members').'?committee_id='.$alternate->getCommittee_id();

        $this->vars = [
            'alternate'  => $alternate,
            'committee'  => $alternate->getCommittee(),
            'seat'       => $alternate->getSeat(),
            'term'       => $alternate->getTerm(),
            'return_url' => $return_url
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/alternates/updateForm.twig', $this->vars);
    }
}
