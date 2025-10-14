<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Committees\Notes\Update;

use Application\Models\Committees\Note;

class View extends \Web\View
{
    public function __construct(Note $n)
    {
        parent::__construct();

        $this->vars = [
            'note'       => $n,
            'committee'  => $n->getCommittee(),
            'return_url' => parent::generateUri('committees.applications', ['committee_id'=>$n->getCommittee_id()])
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/committees/notes/updateForm.twig', $this->vars);
    }
}
