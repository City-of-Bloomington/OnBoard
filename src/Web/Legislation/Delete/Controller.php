<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
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
        if (!empty($_REQUEST['legislation_id'])) {
            try { $legislation = new Legislation($_REQUEST['legislation_id']); }
            catch (\Exception $e) { $_SESSION['errorMesssages'][] = $e->getMessage(); }
        }

        if (isset($legislation)) {
            $committee_id = $legislation->getCommittee_id();
            $year         = $legislation->getYear();
            $return_url   = \Web\View::generateUrl('legislation.index')."?committee_id=$committee_id;year=$year";

            $legislation->delete();

            header("Location: $return_url");
            exit();
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
        return !empty($_REQUEST['legislation_id'])
            && LegislationTable::hasDepartment($department_id, (int)$_REQUEST['legislation_id']);
    }
}
