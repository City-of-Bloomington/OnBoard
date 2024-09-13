<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Committees\Statutes\Update;

use Application\Models\CommitteeStatute;

class View extends \Web\View
{
    public function __construct(CommitteeStatute $statute)
    {
        parent::__construct();

        $this->vars = [
            'statute'   => $statute,
            'committee' => $statute->getCommittee()
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/committees/statutes/updateForm.twig', $this->vars);
    }
}
