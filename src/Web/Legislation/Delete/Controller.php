<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Legislation\Delete;

use Application\Models\Legislation\Legislation;
use Application\Models\Legislation\LegislationTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\Vew
    {
        if (!empty($params['id'])) {
            try { $legislation = new Legislation($params['id']); }
            catch (\Exception $e) { $_SESSION['errorMesssages'][] = $e->getMessage(); }
        }

        if (isset($legislation)) {
            $committee_id = $legislation->getCommittee_id();
            $year         = $legislation->getYear();
            $return_url   = \Web\View::generateUrl('legislation.index', ['committee_id'=>$committee_id])."?year=$year";

            $legislation->delete();

            header("Location: $return_url");
            exit();
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
        return !empty($params['id'])
            && LegislationTable::hasDepartment($department_id, (int)$params['id']);
    }
}
