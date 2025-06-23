<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Applicants\Info;

use Application\Models\Applicant;
use Application\Models\ApplicantTable;
use Application\Models\CommitteeTable;

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
        }

        return new \Web\Views\NotFoundView();
    }
}
