<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\People\View;

use Application\Models\Committee;
use Application\Models\LiaisonTable;
use Application\Models\Person;

class View extends \Web\View
{
    public function __construct(Person $person, bool $disableButtons=false)
    {
        parent::__construct();

        $this->vars = [
            'person'      => $person,
            'members'     => self::members ($person),
            'liaisons'    => self::liaisons($person),
            'actionLinks' => $disableButtons ? null : self::actionLinks($person)
        ];
    }

    public function render(): string
    {
        return $this->twig->render("{$this->outputFormat}/people/info.twig", $this->vars);
    }

    private static function actionLinks(Person $person): array
    {
        $links = [];
        if (parent::isAllowed('people', 'update')) {
            $links[] = [
                'url'   => parent::generateUri('people.update', ['id'=>$person->getId()]),
                'label' => parent::_('person_edit'),
                'class' => 'edit'
            ];
        }
        if (parent::isAllowed('people', 'delete') && $person->isSafeToDelete()) {
            $links[] = [
                'url'   => parent::generateUri('people.delete', ['id'=>$person->getId()]),
                'label' => parent::_('person_delete'),
                'class' => 'delete'
            ];
        }
        return $links;
    }

    private static function members(Person $p): array
    {
        $out = [];
        foreach ($p->getMembers() as $m) {
            $c = $m->getCommittee();

            $out[] = [
                'member_id'      => $m->getId(),
                'committee_id'   => $c->getId(),
                'committee_name' => $c->getName(),
                'startDate'      => $m->getStartDate(),
                'endDate'        => $m->getEndDate(),
                'offices'        => $p->getOffices($c)
            ];
        }
        return $out;
    }

    private static function liaisons(Person $p): array
    {
        $out  = [];
        $data = LiaisonTable::personLiaisonData(['person_id'=>$p->getId()]);
        foreach ($data['results'] as $l) {
            $out[] = [
                'committee_id'   => $l['committee_id'],
                'committee_name' => $l['committee'],
                'type'           => $l['type']
            ];
        }
        return $out;
    }
}
