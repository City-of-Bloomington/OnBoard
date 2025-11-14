<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\People\View;

use Application\Models\ApplicationTable;
use Application\Models\Committee;
use Application\Models\LiaisonTable;
use Application\Models\Person;

class View extends \Web\View
{
    public function __construct(Person $person, bool $disableButtons=false)
    {
        parent::__construct();

        $return_url = parent::generateUrl('people.view', ['person_id'=>$person->getId()]);

        $this->vars = [
            'person'               => $person,
            'members'              => self::members              ($person),
            'liaisons'             => self::liaisons             ($person),
            'emails'               => self::emails               ($person, $return_url),
            'phones'               => self::phones               ($person, $return_url),
            'applicantFiles'       => self::applicantFiles       ($person, $return_url),
            'applications_current' => self::applications_current ($person, $return_url),
            'applications_archived'=> self::applications_archived($person, $return_url),
            'actionLinks' => $disableButtons ? null : self::actionLinks($person),
            'return_url'  => $return_url
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/people/info.twig', $this->vars);
    }

    private static function breadcrumbs(Person $p): array
    {
        return [
            parent::_(['person', 'people', 10]) => parent::generateUri('people.index'),
            $p->getFullname() => null,
        ];
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
        return $links;
    }

    public static function members(Person $p): array
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

    public static function liaisons(Person $p): array
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

    public static function emails(Person $p, string $return_url): array
    {
        $out = [];
        $canEdit   = parent::isAllowed('emails', 'update');
        $canDelete = parent::isAllowed('emails', 'delete');
        foreach ($p->getEmails() as $e) {
            $links = [];
            if ($canEdit) {
                $links[] = [
                    'url'   => parent::generateUri('emails.update', ['person_id'=>$p->getId(), 'email_id'=>$e->getId()])."?return_url=$return_url",
                    'label' => parent::_('email_edit'),
                    'class' => 'edit'
                ];
            }
            if ($canDelete) {
                $links[] = [
                    'url'   => parent::generateUri('emails.delete', ['person_id'=>$p->getId(), 'email_id'=>$e->getId()])."?return_url=$return_url",
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

    public static function phones(Person $p, string $return_url): array
    {
        $out = [];
        $canEdit   = parent::isAllowed('phones', 'update');
        $canDelete = parent::isAllowed('phones', 'delete');
        foreach ($p->getPhones() as $e) {
            $links = [];
            if ($canEdit) {
                $links[] = [
                    'url'   => parent::generateUri('phones.update', ['person_id'=>$p->getId(), 'phone_id'=>$e->getId()])."?return_url=$return_url",
                    'label' => parent::_('phone_edit'),
                    'class' => 'edit'
                ];
            }
            if ($canDelete) {
                $links[] = [
                    'url'   => parent::generateUri('phones.delete', ['person_id'=>$p->getId(), 'phone_id'=>$e->getId()])."?return_url=$return_url",
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

    public static function applicantFiles(Person $p, string $return_url): array
    {
        $canDownload = parent::isAllowed('applicantFiles', 'download');
        $canDelete   = parent::isAllowed('applicantFiles', 'delete');

        if (!$canDownload) { return []; }

        $data = [];
        foreach ($p->getFiles() as $f) {
            $links = [];
            if ($canDelete) {
                $links[] = [
                    'url'   => parent::generateUri('applicantFiles.delete', ['applicantFile_id'=>$f->getId()])."?return_url=$return_url",
                    'label' => parent::_('delete'),
                    'class' => 'delete'
                ];
            }
            $data[] = [
                'id'          => $f->getId(),
                'filename'    => $f->getFilename(),
                'updated'     => $f->getUpdated(DATE_FORMAT),
                'actionLinks' => $links
            ];
        }
        return $data;
    }

    public static function applications_current(Person $p, string $return_url): array
    {
        if (!parent::isAllowed('applicants', 'index')) { return []; }

        $canArchive = parent::isAllowed('applications', 'archive');
        $canDelete  = parent::isAllowed('applications', 'delete');

        $tab  = new ApplicationTable();
        $apps = $tab->find(['current'=>time(), 'person_id'=>$p->getId()], 'created desc');
        $data = [];
        foreach ($apps as $a) {
            $links  = [];
            if ($canArchive) {
                $links[] = [
                    'url'   => parent::generateUri('applications.archive', ['application_id'=>$a->getId()])."?return_url=$return_url",
                    'label' => parent::_('application_archive'),
                    'class' => 'archive'
                ];
            }
            if ($canDelete) {
                $links[] = [
                    'url'   => parent::generateUri('applications.delete', ['application_id'=>$a->getId()])."?return_url=$return_url",
                    'label' => parent::_('application_delete'),
                    'class' => 'delete'
                ];
            }

            $c      = $a->getCommittee();
            $data[] = [
                'id'             => $a->getId(),
                'person_id'      => $a->getPerson_id(),
                'person'         => $p->getFullname(),
                'committee_id'   => $c->getId(),
                'committee'      => $c->getCode() ?? $c->getName(),
                'created'        => $a->getCreated(DATE_FORMAT),
                'expires'        => $a->getExpires(DATE_FORMAT),
                'referredFrom'   => $a->getReferredFrom(),
                'referredOther'  => $a->getReferredOther(),
                'interest'       => $a->getInterest(),
                'qualifications' => $a->getQualifications(),
                'actionLinks'    => $links
            ];
        }
        return $data;
    }

    public static function applications_archived(Person $p, string $return_url): array
    {
        if (!parent::isAllowed('applicants', 'index')) { return []; }

        $canUnArchive = parent::isAllowed('applications', 'unarchive');
        $canDelete    = parent::isAllowed('applications', 'delete');

        $tab  = new ApplicationTable();
        $apps = $tab->find(['archived'=>time(), 'person_id'=>$p->getId()], 'archived desc');
        $data = [];
        foreach ($apps as $a) {
            $links  = [];
            if ($canUnArchive) {
                $links[] = [
                    'url'   => parent::generateUri('applications.unarchive', ['application_id'=>$a->getId()])."?return_url=$return_url",
                    'label' => parent::_('application_unarchive'),
                    'class' => 'unarchive'
                ];
            }
            if ($canDelete) {
                $links[] = [
                    'url'   => parent::generateUri('applications.delete', ['application_id'=>$a->getId()])."?return_url=$return_url",
                    'label' => parent::_('application_delete'),
                    'class' => 'delete'
                ];
            }

            $c      = $a->getCommittee();
            $data[] = [
                'id'           => $a->getId(),
                'person_id'    => $a->getPerson_id(),
                'person'       => $p->getFullname(),
                'committee_id' => $c->getId(),
                'committee'    => $c->getCode() ?? $c->getName(),
                'created'      => $a->getCreated (DATE_FORMAT),
                'archived'     => $a->getArchived(DATE_FORMAT),
                'actionLinks'  => $links
            ];
        }
        return $data;
    }
}
