<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Legislation\Actions\Add;

use Application\Models\Legislation\Action;
use Application\Models\Legislation\ActionsTable;
use Application\Models\Legislation\LegislationTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_REQUEST['legislation_id']) && !empty($_REQUEST['type_id'])) {
            try {
                $action = new Action();
                $action->setLegislation_id($_REQUEST['legislation_id']);
                $action->setType_id       ($_REQUEST['type_id'       ]);
            }
            catch (\Exception $e) {
                unset($action);
                $_SESSION['errorMessages'][] = $e->getMessage();
            }
        }

        if (isset($action)) {
            echo "Action ready to edit\n";
            if (isset($_POST['legislation_id'])) {
                try {
                    $action->setLegislation_id($_POST['legislation_id']);
                    $action->setType_id       ($_POST['type_id'       ]);
                    $action->setActionDate    ($_POST['actionDate'    ], 'Y-m-d');
                    $action->setOutcome       ($_POST['outcome'       ]);
                    $action->setVote          ($_POST['vote'          ]);

                    $action->save();
                    $url = \Web\View::generateUrl('legislation.view', [
                        'legislation_id' => $action->getLegislation_id(),
                        'committee_id'   => $action->getLegislation()->getCommittee_id()
                    ]);
                    header("Location: $url");
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
            }

            return new \Web\Legislation\Actions\Update\View($action);
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
        if (!empty($_REQUEST['legislation_id'])) {
            return LegislationTable::hasDepartment($department_id, (int)$_REQUEST['legislation_id']);
        }

        return false;
    }
}
