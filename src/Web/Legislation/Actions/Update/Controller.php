<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Legislation\Actions\Update;

use Application\Models\Legislation\Action;
use Application\Models\Legislation\ActionsTable;
use Application\Models\Legislation\LegislationTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($params['id'])) {
            try { $action = new Action($params['id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        if (isset($action)) {
            if (isset($_POST['legislation_id'])) {
                try {
                    $action->setLegislation_id($_POST['legislation_id']);
                    $action->setType_id       ($_POST['type_id'       ]);
                    $action->setActionDate    ($_POST['actionDate'    ], 'Y-m-d');
                    $action->setOutcome       ($_POST['outcome'       ]);
                    $action->setVote          ($_POST['vote'          ]);

                    $action->save();
                    header('Location: ').\Web\View::generateUrl('legislation.view', [
                        'id'       => $action->getLegislation_id(),
                        'committe' => $action->getLegislation()->getCommittee_id()
                    ]);
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
            }

            return new View($action);
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
        if (!empty($params['id'])) {
            return ActionsTable::hasDepartment($department_id, (int)$params['id']);
        }

        if (!empty($_REQUEST['legislation_id'])) {
            return LegislationTable::hasDepartment($department_id, (int)$_REQUEST['legislation_id']);
        }

        return false;
    }
}
