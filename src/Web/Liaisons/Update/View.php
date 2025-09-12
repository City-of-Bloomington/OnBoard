<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Liaisons\Update;

use Application\Models\Liaison;

class View extends \Web\View
{
    public function __construct(Liaison $liaison)
    {
        parent::__construct();

        $this->vars = [
            'liaison'     => $liaison,
            'committee'   => $liaison->getCommittee(),
            'types'       => self::liaison_types(),
            'breadcrumbs' => self::breadcrumbs($liaison)
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/liaisons/updateForm.twig', $this->vars);
    }

    private static function breadcrumbs(Liaison $l)
    {
        $committee    = $l->getCommittee()->getName();
        $committee_id = $l->getCommittee_id();
        $liaisons     = parent::_(['liaison', 'liaisons', 10]);
        $action       = $l->getId() ? 'edit' : 'add';

        return [
            $committee         => parent::generateUri('committees.info',     ['committee_id'=>$committee_id]),
            $liaisons          => parent::generateUri('committees.liaisons', ['committee_id'=>$committee_id]),
            parent::_($action) => null
        ];
    }

    private static function liaison_types(): array
    {
        $options = [];
        foreach (Liaison::$types as $t) {
            $options[] = ['value'=>$t];
        }
        return $options;
    }
}
