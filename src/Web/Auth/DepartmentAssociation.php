<?php
/**
 * Checks if a controller is loading a record associated with a given department
 *
 * @copyright 2024-2026 City of Bloomington, Indiana
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
    public static $params = [
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

    public function assert(Acl $acl, ?RoleInterface $role=null, ?ResourceInterface $resource=null, $privilege=null)
    {
        if (isset($_SESSION['USER'])) {
            $did = (int)$_SESSION['USER']->getDepartment_id();

            switch ($resource) {
                case 'committees.notes':
                case 'committees.file':
                case 'committees':
                case 'legislation':
                    return self::check_department('committee_id', $did);

                case 'applications':
                    return self::check_department('application_id', $did);

                case 'applicantFiles':
                    return self::check_department('applicantFile_id', $did);

                case 'meetingFiles':
                    return $privilege == 'add'
                        ? self::check_department(    'meeting_id', $did)
                        : self::check_department('meetingFile_id', $did);

                case 'legislationFiles':
                    return $privilege == 'add'
                        ? self::check_department(    'legislation_id', $did)
                        : self::check_department('legislationFile_id', $did);

                case 'legislationActions':
                    return $privilege == 'add'
                        ? self::check_department(      'legislation_id', $did)
                        : self::check_department('legislationAction_id', $did);

                case 'reports':
                    return $privilege == 'add'
                        ? self::check_department('committee_id', $did)
                        : self::check_department(   'report_id', $did);
            }
       }
       return false;
    }

    private static function check_department(string $param_name, int $dept_id): bool
    {
        if ( !empty( $_REQUEST[$param_name])) {
            $t = self::$params[$param_name];
            $table = new $t();
            return $table->hasDepartment($dept_id, (int)$_REQUEST[$param_name]);
        }
        return false;
    }
}
