<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Applicants\Info;

use Application\Models\Applicant;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_REQUEST['applicant_id'])) {
            try { $applicant = new Applicant($_REQUEST['applicant_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        if (isset($applicant)) {
            return new View($applicant);

            $this->template->blocks[] = new Block('applicants/info.inc', ['applicant'=>$applicant]);
            $this->template->blocks[] = new Block('applications/list.inc', [
                'applicant'    => $applicant,
                'applications' => $applicant->getApplications(['current'=>time()]),
                'title'        => $this->template->_('applications_current'),
                'type'         => 'current'
            ]);
            $this->template->blocks[] = new Block('applications/list.inc', [
                'applicant'    => $applicant,
                'applications' => $applicant->getApplications(['archived'=>time()]),
                'title'        => $this->template->_('applications_archived'),
                'type'         => 'archived'
            ]);
        }

        return new \Web\Views\NotFoundView();
    }
}
