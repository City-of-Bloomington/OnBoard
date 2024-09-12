<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\LegislationActions\Update;

use Application\Models\Legislation\Action;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_REQUEST['legislationAction_id'])) {
            try { $action = new Action($_REQUEST['legislationAction_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }
        else {
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
                    $return_url = \Web\View::generateUrl('legislation.view').'?legislation_id='.$action->getLegislation_id();
                    header("Location: $return_url");
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
            }

            return new View($action);
        }

        return new \Web\Views\NotFoundView();
    }
}
