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
            'emails'      => self::emails  ($person),
            'phones'      => self::phones  ($person),
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
                'url'   => parent::generateUri('people.update', ['person_id'=>$person->getId()]),
                'label' => parent::_('person_edit'),
                'class' => 'edit'
            ];
        }
        if (parent::isAllowed('people', 'delete') && $person->isSafeToDelete()) {
            $links[] = [
                'url'   => parent::generateUri('people.delete', ['person_id'=>$person->getId()]),
                'label' => parent::_('person_delete'),
                'class' => 'delete'
            ];
        }
        if (!$person->getUsername() && parent::isAllowed('users', 'update')) {
            $links[] = [
                'url'   => parent::generateUri('users.update', ['person_id'=>$person->getId()]),
                'label' => parent::_('create_account'),
                'class' => 'add'
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

    private static function emails(Person $p): array
    {
        $out = [];
        $canEdit   = parent::isAllowed('emails', 'update');
        $canDelete = parent::isAllowed('emails', 'delete');
        foreach ($p->getEmails() as $e) {
            $links = [];
            if ($canEdit) {
                $links[] = [
                    'url'   => parent::generateUri('emails.update', ['person_id'=>$p->getId(), 'email_id'=>$e->getId()]),
                    'label' => parent::_('email_edit'),
                    'class' => 'edit'
                ];
            }
            if ($canDelete) {
                $links[] = [
                    'url'   => parent::generateUri('emails.delete', ['person_id'=>$p->getId(), 'email_id'=>$e->getId()]),
                    'label' => parent::_('email_delete'),
                    'class' => 'delete'
                ];
            }

            $out[] = [
                'email_id'    => $e->getId(),
                'email'       => $e->getEmail(),
                'person_id'   => $e->getPerson_id(),
                'main'        => $e->getMain(),
                'actionLinks' => $links
            ];
        }
        return $out;
    }

    private static function phones(Person $p): array
    {
        $out = [];
        $canEdit   = parent::isAllowed('phones', 'update');
        $canDelete = parent::isAllowed('phones', 'delete');
        foreach ($p->getPhones() as $e) {
            $links = [];
            if ($canEdit) {
                $links[] = [
                    'url'   => parent::generateUri('phones.update', ['person_id'=>$p->getId(), 'phone_id'=>$e->getId()]),
                    'label' => parent::_('phone_edit'),
                    'class' => 'edit'
                ];
            }
            if ($canDelete) {
                $links[] = [
                    'url'   => parent::generateUri('phones.delete', ['person_id'=>$p->getId(), 'phone_id'=>$e->getId()]),
                    'label' => parent::_('phone_delete'),
                    'class' => 'delete'
                ];
            }
            $out[] = [
                'phone_id'    => $e->getId(),
                'number'      => $e->getNumber(),
                'person_id'   => $e->getPerson_id(),
                'main'        => $e->getMain(),
                'actionLinks' => $links
            ];
        }
        return $out;
    }
}
