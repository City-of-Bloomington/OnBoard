<?php
/**
 * General purpose assertion for committee association
 *
 * This assertion checks that the current user is associated with the
 * same committee as the given resource.  This will not work for citizen
 * applicants to committees, as a single application is associated with multiple
 * committees.
 * @see Application\Authorization\ApplicantAssociation
 *
 * @copyright 2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Authorization;

use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Role\RoleInterface;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Assertion\AssertionInterface;

use Application\Models\LiaisonTable;
use Application\Models\Member;
use Application\Models\Seat;
use Application\Models\Term;

class CommitteeAssociation implements AssertionInterface
{
    public function assert(Acl $acl, RoleInterface $role=null, ResourceInterface $resource=null, $privilege=null)
    {
        if (isset($_SESSION['USER'])) {

            // What committee is the resource associated with
            $committee_id = self::getCommittee_id();
            if (!$committee_id) { return false; }

            // What committee is the user associated with
            switch ($role->__toString()) {
                case 'Liaison':
                    return LiaisonTable::isLiaison($_SESSION['USER']->getId(), $committee_id);
                break;

                case 'Administrator':
                    return true;
                break;

                default:
                    return false;
            }
        }
        return false;
    }

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
        return null;
    }
}
