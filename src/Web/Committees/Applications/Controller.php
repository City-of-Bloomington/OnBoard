<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Committees\Applications;

use Application\Models\Committee;
use Application\Models\CommitteeTable;
use \Web\View;
use \Web\Auth\RequiresDepartment;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): View
    {
        if (!empty($_GET['committee_id'])) {
            try {
                $committee             = new Committee($_GET['committee_id']);
                $seats                 = [];

                foreach ($committee->getSeats(['current'=>true]) as $a) {
                    $seats[] = $a;
                }

                return View::isAllowed('applications', 'report')
                       ? new ReportView($committee, $seats)
                       : new   ListView($committee);

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
        return !empty($_GET['committee_id'])
            && CommitteeTable::hasDepartment($department_id, (int)$_GET['committee_id']);
    }
}
