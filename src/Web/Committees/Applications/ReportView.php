<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Committees\Applications;

use Application\Models\Committee;
use Web\View;

class ReportView extends View
{
    public function __construct(Committee $committee,
                                array     $seats)
    {
        parent::__construct();

        $this->vars = [
            'committee'             => $committee,
            'seats'                 => $seats,
            'applications_current'  => $this->application_data_current($committee),
            'applications_archived' => $this->application_data_archived($committee)
        ];
    }

    public function render(): string
    {
        return $this->twig->render($this->outputFormat.'/applications/reportForm.twig', $this->vars);
    }

    private function application_data_current(Committee $committee): array
    {
        $canArchive = parent::isAllowed('applications', 'archive');
        $canDelete  = parent::isAllowed('applications', 'delete');

        $data = [];
        foreach ($committee->getApplications(['current' =>time()]) as $a) {
            $links  = [];
            if ($canArchive) {
                $links[] = [
                    'url'   => parent::generateUri('applications.archive', ['application_id'=>$a->getId()]),
                    'label' => $this->_('application_archive'),
                    'class' => 'archive'
                ];
            }
            if ($canDelete) {
                $links[] = [
                    'url'   => parent::generateUri('applications.delete', ['application_id'=>$a->getId()]),
                    'label' => $this->_('application_delete'),
                    'class' => 'delete'
                ];
            }

            $p      = $a->getApplicant();
            $data[] = [
                'id'           => $a->getId(),
                'applicant_id' => $a->getApplicant_id(),
                'applicant'    => "{$p->getFirstname()} {$p->getLastname()}",
                'created'      => $a->getCreated(),
                'expires'      => $a->getExpires(),
                'actionLinks'  => $links
            ];
        }
        return $data;
    }

    private function application_data_archived(Committee $committee): array
    {
        $canUnArchive = parent::isAllowed('applications', 'unarchive');
        $canDelete    = parent::isAllowed('applications', 'delete');

        $data = [];
        foreach ($committee->getApplications(['archived' =>time()]) as $a) {
            $links  = [];
            if ($canUnArchive) {
                $links[] = [
                    'url'   => parent::generateUri('applications.unarchive', ['application_id'=>$a->getId()]),
                    'label' => $this->_('application_unarchive'),
                    'class' => 'unarchive'
                ];
            }
            if ($canDelete) {
                $links[] = [
                    'url'   => parent::generateUri('applications.delete', ['application_id'=>$a->getId()]),
                    'label' => $this->_('application_delete'),
                    'class' => 'delete'
                ];
            }

            $p      = $a->getApplicant();
            $data[] = [
                'id'           => $a->getId(),
                'applicant_id' => $a->getApplicant_id(),
                'applicant'    => "{$p->getFirstname()} {$p->getLastname()}",
                'created'      => $a->getCreated(),
                'archived'     => $a->getArchived(),
                'actionLinks'  => $links
            ];
        }
        return $data;
    }
}
