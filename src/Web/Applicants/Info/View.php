<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Applicants\Info;

use Application\Models\Applicant;

class View extends \Web\View
{
    public function __construct(Applicant $applicant)
    {
        parent::__construct();

        $this->vars = [
            'applicant'            => $applicant,
            'applications_current' => $this->application_data_current ($applicant),
            'applications_archived'=> $this->application_data_archived($applicant),
            'actionLinks'          => $this->actionLinks($applicant)
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/applicants/info.twig', $this->vars);
    }

    private function actionLinks(Applicant $applicant): array
    {
        $out = [];
        if (parent::isAllowed('applicants', 'update')) {
            $out[] = [
                'url'   => parent::generateUri('applicants.update').'?applicant_id='.$applicant->getId(),
                'label' => _('applicant_edit'),
                'class' => 'edit'
            ];
        }
        return $out;
    }

    private function application_data_current(Applicant $applicant): array
    {
        $canArchive = parent::isAllowed('applications', 'archive');
        $canDelete  = parent::isAllowed('applications', 'delete');

        $data = [];
        foreach ($applicant->getApplications(['current' =>time()]) as $a) {
            $links  = [];
            if ($canArchive) {
                $links[] = [
                    'url'   => parent::generateUri('applications.archive', ['id'=>$a->getId()]),
                    'label' => $this->_('application_archive'),
                    'class' => 'edit'
                ];
            }
            if ($canDelete) {
                $links[] = [
                    'url'   => parent::generateUri('applications.delete', ['id'=>$a->getId()]),
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

    private function application_data_archived(Applicant $applicant): array
    {
        $canUnArchive = parent::isAllowed('applications', 'unarchive');
        $canDelete    = parent::isAllowed('applications', 'delete');

        $data = [];
        foreach ($applicant->getApplications(['archived' =>time()]) as $a) {
            $links  = [];
            if ($canUnArchive) {
                $links[] = [
                    'url'   => parent::generateUri('applications.unarchive', ['id'=>$a->getId()]),
                    'label' => $this->_('application_unarchive'),
                    'class' => 'edit'
                ];
            }
            if ($canDelete) {
                $links[] = [
                    'url'   => parent::generateUri('applications.delete', ['id'=>$a->getId()]),
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
