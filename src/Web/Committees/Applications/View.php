<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Committees\Applications;

use Application\Models\ApplicationTable;
use Application\Models\Committee;
use Application\Models\Notifications\DefinitionTable;
use Application\Models\Notifications\SubscriptionTable;

class View extends \Web\View
{
    public function __construct(Committee $committee)
    {
        parent::__construct();

        $this->vars = [
            'committee'             => $committee,
            'applications_current'  => self::applications_current ($committee),
            'applications_archived' => self::applications_archived($committee),
            'actionLinks'           => self::actionLinks($committee)
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/applications/list.twig', $this->vars);
    }

    private static function actionLinks(Committee $c): array
    {
        $ret   = parent::generateUrl('committees.applications', ['committee_id'=>$c->getId()]);
        $event = DefinitionTable::APPLICATION_NOTICE;

        return \Web\Notifications\View::actionLinksForSubscriptions($event, $c->getId(), $ret);
    }

    private static function applications_current(Committee $c): array
    {
        $canArchive = parent::isAllowed('applications', 'archive');
        $canDelete  = parent::isAllowed('applications', 'delete');
        $url        = parent::current_url();

        $tab  = new ApplicationTable();
        $apps = $tab->find(['current'=>time(), 'committee_id'=>$c->getId()], 'created desc');
        $data = [];
        foreach ($apps as $a) {
            $links  = [];
            if ($canArchive) {
                $links[] = [
                    'url'   => parent::generateUri('applications.archive', ['application_id'=>$a->getId()])."?return_url=$url",
                    'label' => parent::_('application_archive'),
                    'class' => 'archive'
                ];
            }
            if ($canDelete) {
                $links[] = [
                    'url'   => parent::generateUri('applications.delete', ['application_id'=>$a->getId()])."?return_url=$url",
                    'label' => parent::_('application_delete'),
                    'class' => 'delete'
                ];
            }

            $p      = $a->getPerson();
            $data[] = [
                'id'           => $a->getId(),
                'person_id'    => $a->getPerson_id(),
                'person'       => "{$p->getFirstname()} {$p->getLastname()}",
                'created'      => $a->getCreated(DATE_FORMAT),
                'expires'      => $a->getExpires(DATE_FORMAT),
                'actionLinks'  => $links
            ];
        }
        return $data;
    }

    private static function applications_archived(Committee $c): array
    {
        $canUnArchive = parent::isAllowed('applications', 'unarchive');
        $canDelete    = parent::isAllowed('applications', 'delete');
        $url          = parent::current_url();

        $tab  = new ApplicationTable();
        $apps = $tab->find(['archived'=>time(), 'committee_id'=>$c->getId()], 'archived desc');
        $data = [];
        foreach ($apps as $a) {
            $links  = [];
            if ($canUnArchive) {
                $links[] = [
                    'url'   => parent::generateUri('applications.unarchive', ['application_id'=>$a->getId()])."?return_url=$url",
                    'label' => parent::_('application_unarchive'),
                    'class' => 'unarchive'
                ];
            }
            if ($canDelete) {
                $links[] = [
                    'url'   => parent::generateUri('applications.delete', ['application_id'=>$a->getId()])."?return_url=$url",
                    'label' => parent::_('application_delete'),
                    'class' => 'delete'
                ];
            }

            $p      = $a->getPerson();
            $data[] = [
                'id'           => $a->getId(),
                'person_id'    => $a->getPerson_id(),
                'person'       => "{$p->getFirstname()} {$p->getLastname()}",
                'created'      => $a->getCreated (DATE_FORMAT),
                'archived'     => $a->getArchived(DATE_FORMAT),
                'actionLinks'  => $links
            ];
        }
        return $data;
    }
}
