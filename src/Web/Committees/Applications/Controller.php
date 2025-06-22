<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
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
        if (!empty($_REQUEST['committee_id'])) {
            try {
                $committee = new Committee($_REQUEST['committee_id']);
                $seats     = [];

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
     * ACL will call this function before invoking the Controller
     *
     * When a role needs to check the Department Association, the ACL will
     * be checked before invoking the Controller.  This function must be called
     * statically.  The current route parameters will be passed.  These parameters
     * will be the same as would be passed to __invoke().
     *
     * @see Web\Auth\DepartmentAssociation
     * @see access_control.php
     */
    public static function hasDepartment(int $department_id, array $params): bool
    {
        return !empty($_REQUEST['committee_id'])
            && CommitteeTable::hasDepartment($department_id, (int)$_REQUEST['committee_id']);
    }
}
