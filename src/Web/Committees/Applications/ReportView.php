<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Committees\Applications;

use Application\Models\Committee;
use Application\Models\Notifications\SubscriptionTable;

class ReportView extends \Web\View
{
    public function __construct(Committee $committee,
                                array     $seats)
    {
        parent::__construct();

        $this->vars = [
            'committee'             => $committee,
            'seats'                 => $seats,
            'applications_current'  => self::applications_current ($committee),
            'applications_archived' => self::applications_archived($committee),
            'actionLinks'           => self::actionLinks($committee)
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/applications/reportForm.twig', $this->vars);
    }

    private static function actionLinks(Committee $c): array
    {
        $ret     = parent::generateUrl('committees.applications', ['committee_id'=>$c->getId()]);
        $event = 'Web\Applicants\Apply::notice';
        $sub   = null;

        $links = [];
        if ( isset($_SESSION['USER'])) {
            $sub = $_SESSION['USER']->hasNotificationSubscription($event, $c->getId());
            if ($sub) {
                if (parent::isAllowed('profile.notifications', 'delete')) {
                    $links[] = [
                        'url'   => parent::generateUri('profile.notifications.delete', ['subscription_id'=>$sub->getId()])."?return_url=$ret",
                        'label' => parent::_('notification_subscription_delete'),
                        'class' => 'notifications_off'
                    ];
                }
            }
            else {
                if (parent::isAllowed('profile.notifications', 'add')) {
                    $params  = http_build_query(['committee_id'=>$c->getId(), 'event'=>$event, 'return_url'=>$ret]);
                    $links[] = [
                        'url'   => parent::generateUri('profile.notifications.add')."?$params",
                        'label' => parent::_('notification_subscription_add'),
                        'class' => 'notifications'
                    ];
                }
            }
        }
        return $links;
    }

    private static function applications_current(Committee $committee): array
    {
        $canArchive = parent::isAllowed('applications', 'archive');
        $canDelete  = parent::isAllowed('applications', 'delete');
        $url        = parent::current_url();

        $data = [];
        foreach ($committee->getApplications(['current' =>time()]) as $a) {
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

    private static function applications_archived(Committee $committee): array
    {
        $canUnArchive = parent::isAllowed('applications', 'unarchive');
        $canDelete    = parent::isAllowed('applications', 'delete');
        $url          = parent::current_url();

        $data = [];
        foreach ($committee->getApplications(['archived' =>time()]) as $a) {
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
