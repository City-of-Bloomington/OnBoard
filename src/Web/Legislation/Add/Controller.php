<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Legislation\Add;

use Application\Models\Legislation\Legislation;
use Application\Models\Legislation\LegislationTable;
use Application\Models\CommitteeTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        $legislation = new Legislation();

        if (isset($legislation)) {
            if (!$legislation->getCommittee_id()) {
                if (!empty($_REQUEST['committee_id'])) {
                    try { $legislation->setCommittee_id($_REQUEST['committee_id']); }
                    catch (\Exception $e) { $_SESSION['errorMesssages'][] = $e->getMessage(); }
                }
            }

            if (!empty($_REQUEST['parent_id'])) {
                try {
                    $parent = new Legislation($_REQUEST['parent_id']);
                    $legislation->setParent_id   ($parent->getId());
                    $legislation->setCommittee_id($parent->getCommittee_id());
                }
                catch (\Exception $e) { $_SESSION['errorMesssages'][] = $e->getMessage(); }
            }

            if (!empty($_REQUEST['type_id'])) {
                try { $legislation->setType_id($_REQUEST['type_id']); }
                catch (\Exception $e) { $_SESSION['errorMesssages'][] = $e->getMessage(); }
            }
        }

        if (isset($legislation) && $legislation->getCommittee_id()) {
            $_SESSION['return_url'] = !empty($_REQUEST['return_url'])
                                 ? urldecode($_REQUEST['return_url'])
                                 : ($legislation->getId()
                                     ? \Web\View::generateUrl('legislation.view', ['id'=>$legislation->getId()])
                                     : \Web\View::generateUrl('legislation.index'));

            if (isset($_POST['number'])) {
                try {
                    // Needed for the new Bootstrap boolean toggle
                    if (!isset($_POST['amendsCode'])) { $_POST['amendsCode'] = false; }

                    $legislation->handleUpdate($_POST);
                    $legislation->save();

                    $return_url = $_SESSION['return_url'];
                    unset($_SESSION['return_url']);

                    header("Location: $return_url");
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMesssages'][] = $e->getMessage(); }
            }

            return new \Web\Legislation\Update\View($legislation, $_SESSION['return_url']);
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
        if (!empty($_REQUEST['committee_id'])) {
            return CommitteeTable::hasDepartment($department_id, (int)$_GET['committee_id']);
        }
        if (!empty($params['id'])) {
            return LegislationTable::hasDepartment($department_id, (int)$params['id']);
        }
        return false;
    }
}
