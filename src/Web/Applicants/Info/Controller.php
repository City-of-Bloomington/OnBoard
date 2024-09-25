<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
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

    /**
     * ACL will call this function when a role needs to check the Department Association
     *
     * @see Web\Auth\DepartmentAssociation
     */
    public static function hasDepartment(int $department_id): bool
    {
        if (!empty($_GET['committee_id'])) {
            return CommitteeTable::hasDepartment($department_id, (int)$_GET['committee_id']);
        }
        if (!empty($_REQUEST['applicant_id'])) {
            return ApplicantTable::hasDepartment($department_id, (int)$_REQUEST['applicant_id']);
        }
        return false;
    }
}
