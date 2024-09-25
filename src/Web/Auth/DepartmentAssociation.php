<?php
/**
 * Checks if a controller is loading a record associated with a given department
 *
 * @copyright 2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Auth;

use Laminas\Permissions\Acl\Acl;
use Laminas\Permissions\Acl\Role\RoleInterface;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Assertion\AssertionInterface;

use Application\Models\CommitteeTable;
use Application\Models\MeetingFilesTable;
use Application\Models\Legislation\LegislationTable;
use Application\Models\Legislation\LegislationFilesTable;
use Application\Models\Legislation\ActionsTable;
use Application\Models\Reports\ReportsTable;
use Application\Models\ApplicantTable;
use Application\Models\ApplicantFilesTable;

class DepartmentAssociation implements AssertionInterface
{
    public function assert(Acl $acl, RoleInterface $role=null, ResourceInterface $resource=null, $privilege=null)
    {
        if (isset($_SESSION['USER'])) {
            $did = $_SESSION['USER']->getDepartment_id();

            global $ROUTES;
            $r = $ROUTES->getMap()->getRoute("$resource.$privilege");
            if ($r) {
                $controller = $r->handler;
                return $controller::hasDepartment($did);
            }
       }
       return false;
    }
}
