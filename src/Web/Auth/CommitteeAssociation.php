<?php
/**
 * General purpose assertion for committee association
 *
 * This assertion checks that the current user is associated with the
 * same committee as the given resource.  This will not work for citizen
 * applicants to committees, as a single application is associated with multiple
 * committees.
 *
 * @copyright 2020-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Auth;

use Laminas\Permissions\Acl\Acl;
use Laminas\Permissions\Acl\Role\RoleInterface;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Assertion\AssertionInterface;

use Application\Models\ApplicantFilesTable;
use Application\Models\ApplicantTable;
use Application\Models\LiaisonTable;
use Application\Models\Member;
use Application\Models\MemberTable;
use Application\Models\Office;
use Application\Models\Seat;
use Application\Models\Term;

class CommitteeAssociation implements AssertionInterface
{
    public function assert(Acl $acl, ?RoleInterface $role=null, ?ResourceInterface $resource=null, $privilege=null)
    {
        if (isset($_SESSION['USER'])) {
            $user_id      = $_SESSION['USER']->getId();
            $committee_id = self::getCommittee_id();

            switch ($role->__toString()) {
                case 'Appointer':
                    if ($committee_id) {
                        $t = new MemberTable();
                        return $t->isMember($user_id, $committee_id);
                    }

                    if (!empty($_REQUEST['applicant_id'])) {
                        $t = new ApplicantTable();
                        return $t->shareCommittee($user_id, (int)$_REQUEST['applicant_id']);
                    }

                    if (!empty($_REQUEST['applicantFile_id'])) {
                        $t = new ApplicantFilesTable();
                        return $t->shareCommittee($user_id, (int)$_REQUEST['applicantFile_id']);
                    }
                break;

                case 'Liaison':
                    if ($committee_id) {
                        $t = new LiaisonTable();
                        return $t->isLiaison($user_id, $committee_id);
                    }
                break;

                case 'Administrator':
                    return true;
                break;

                default:
            }
        }
        return false;
    }

    /**
     * Determine if we're dealing with a single committee by looking at the request parameters
     */
    private static function getCommittee_id(): ?int
    {
        if (!empty($_REQUEST['committee_id'])) {
            return (int)$_REQUEST['committee_id'];
        }
        elseif (!empty($_REQUEST['member_id'])) {
            $member = new Member($_REQUEST['member_id']);
            return (int)$member->getCommittee_id();
        }
        elseif (!empty($_REQUEST['term_id'])) {
            $term = new Term($_REQUEST['term_id']);
            return (int)$term->getCommittee()->getId();
        }
        elseif (!empty($_REQUEST['seat_id'])) {
            $seat = new Seat($_REQUEST['seat_id']);
            return (int)$seat->getCommittee_id();
        }
        elseif (!empty($_REQUEST['office_id'])) {
            $office = new Office($_REQUEST['office_id']);
            return (int)$office->getCommittee_id();
        }
        return null;
    }
}
