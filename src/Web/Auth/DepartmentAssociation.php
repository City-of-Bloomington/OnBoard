<?php
/**
 * Checks if a controller is loading a record associated with a given department
 *
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Auth;

use Laminas\Permissions\Acl\Acl;
use Laminas\Permissions\Acl\Role\RoleInterface;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Assertion\AssertionInterface;

class DepartmentAssociation implements AssertionInterface
{
    private static $params = [
        'committee_id'         => '\Application\Models\CommitteeTable',
        'meeting_id'           => '\Application\Models\MeetingTable',
        'meetingFile_id'       => '\Application\Models\MeetingFilesTable',
        'member_id'            => '\Application\Models\MemberTable',
        'term_id'              => '\Application\Models\TermTable',
        'seat_id'              => '\Application\Models\SeatTable',
        'report_id'            => '\Application\Models\Reports\ReportsTable',
        'alternate_id'         => '\Application\Models\AlternateTable',
        'applicantFile_id'     => '\Application\Models\ApplicantFilesTable',
        'application_id'       => '\Application\Models\ApplicationTable',
        'committeeStatute_id'  => '\Application\Models\CommitteeStatuteTable',
        'legislation_id'       => '\Application\Models\Legislation\LegislationTable',
        'legislationAction_id' => '\Application\Models\Legislation\ActionsTable',
        'legislationFile_id'   => '\Application\Models\Legislation\LegislationFilesTable',
        'liaison_id'           => '\Application\Models\LiaisonTable',
        'office_id'            => '\Application\Models\OfficeTable'
    ];

    public function assert(Acl $acl, RoleInterface $role=null, ResourceInterface $resource=null, $privilege=null)
    {
        if (isset($_SESSION['USER'])) {
            $did = $_SESSION['USER']->getDepartment_id();

            foreach (self::$params as $p=>$t) {
                if (!empty($_REQUEST[$p])) {
                    return $t::hasDepartment((int)$did, (int)$_REQUEST[$p]);
                }
            }
       }
       return false;
    }
}
