<?php
/**
 * Checks if a user is associated with a given record
 *
 * @copyright 2025 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Auth;

use Laminas\Permissions\Acl\Acl;
use Laminas\Permissions\Acl\Role\RoleInterface;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Assertion\AssertionInterface;

class PersonAssociation implements AssertionInterface
{
    private static $params = [
        'email_id' => '\Application\Models\EmailTable',
        'phone_id' => '\Application\Models\PhoneTable'
    ];

    public function assert(Acl $acl, RoleInterface $role=null, ResourceInterface $resource=null, $privilege=null)
    {
        if (isset($_SESSION['USER'])) {
            if (!empty($_REQUEST['person_id'])
                    && $_REQUEST['person_id'] == $_SESSION['USER']->getId()) {
                return true;
            }
            foreach (self::$params as $p=>$t) {
                if (!empty($_REQUEST[$p])) {
                    return $t::hasPerson($_SESSION['USER']->getId(), (int)$_REQUEST[$p]);
                }
            }
        }
        return false;
    }
}
