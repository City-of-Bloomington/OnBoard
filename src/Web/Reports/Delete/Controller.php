<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Reports\Delete;

use Application\Models\Reports\Report;
use Application\Models\Reports\ReportsTable;
use Application\Models\CommitteeTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_GET['report_id'])) {
            try {
                $file         = new Report($_GET['report_id']);
                $committee_id = $file->getCommittee_id();
                $return_url   = \Web\View::generateUrl('reports.index').'?committee_id='.$committee_id;

                $file->delete();

                header("Location: $return_url");
                exit();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
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
        if (!empty($_GET['report_id'])) {
            return ReportsTable::hasDepartment($department_id, (int)$_GET['report_id']);
        }

        return false;
    }
}
