<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Offices\Update;

use Application\Models\Office;
use Application\Models\Committee;

class View extends \Web\View
{
    public function __construct(Office $office)
    {
        parent::__construct();

        $this->vars = [
            'office'      => $office,
            'committee'   => $office->getCommittee(),
            'offices'     => self::offices($office->getCommittee()),
            'breadcrumbs' => self::breadcrumbs($office)
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/offices/updateForm.twig', $this->vars);
    }

    private static function breadcrumbs(Office $o): array
    {
        $committee_id = $o->getCommittee_id();
        $committee    = $o->getCommittee()->getName();
        $members      = parent::_(['member', 'members', 10]);
        $person       = $o->getPerson()->getFullname();

        return [
            $committee  => parent::generateUri('committees.info',    ['committee_id'=>$committee_id]),
            $members    => parent::generateUri('committees.members', ['committee_id'=>$committee_id]),
            parent::_('office_edit') => null
        ];
    }

    private static function offices(Committee $committee): array
    {
        $offices = [];
        foreach ($committee->getOffices() as $o) { $offices[] = $o; }
        return $offices;
    }
}
