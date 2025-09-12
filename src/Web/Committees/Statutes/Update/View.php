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
            'statute'     => $statute,
            'committee'   => $statute->getCommittee(),
            'breadcrumbs' => self::breadcrumbs($statute)
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/committees/statutes/updateForm.twig', $this->vars);
    }

    private static function breadcrumbs(CommitteeStatute $s): array
    {
        $committee    = $s->getCommittee()->getName();
        $committee_id = $s->getCommittee_id();

        if ($s->getId()) {
            $statutes  = $s->getCitation();
            return [
                $committee        => parent::generateUri('committees.info',     ['committee_id'=>$committee_id]),
                $statutes         => parent::generateUri('committees.statutes', ['committee_id'=>$committee_id]),
                parent::_('edit') => null
            ];
        }
        else {
            $statutes  = parent::_(['committeeStatute', 'committeeStatutes', 10]);
            return [
                $committee       => parent::generateUri('committees.info',     ['committee_id'=>$committee_id]),
                $statutes        => parent::generateUri('committees.statutes', ['committee_id'=>$committee_id]),
                parent::_('add') => null
            ];
        }
    }
}
