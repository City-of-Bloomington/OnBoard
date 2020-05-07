<?php
/**
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

class DepartmentAssociation implements AssertionInterface
{
    public function assert(Acl $acl, RoleInterface $role=null, ResourceInterface $resource=null, $privilege=null)
    {
        if (isset($_SESSION['USER'])) {
            $did = $_SESSION['USER']->getDepartment_id();
            if (!empty($_REQUEST[        'committee_id'])) return        CommitteeTable::hasDepartment($did, (int)$_REQUEST[        'committee_id']);
            if (!empty($_REQUEST[      'meetingFile_id'])) return     MeetingFilesTable::hasDepartment($did, (int)$_REQUEST[      'meetingFile_id']);
            if (!empty($_REQUEST[      'legislation_id'])) return      LegislationTable::hasDepartment($did, (int)$_REQUEST[      'legislation_id']);
            if (!empty($_REQUEST[  'legislationFile_id'])) return LegislationFilesTable::hasDepartment($did, (int)$_REQUEST[  'legislationFile_id']);
            if (!empty($_REQUEST['legislationAction_id'])) return          ActionsTable::hasDepartment($did, (int)$_REQUEST['legislationAction_id']);
            if (!empty($_REQUEST[           'report_id'])) return          ReportsTable::hasDepartment($did, (int)$_REQUEST[           'report_id']);
       }
       return false;
    }
}
