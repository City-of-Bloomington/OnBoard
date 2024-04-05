<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */

// NOTE: Needs some work and fixes done

declare(strict_types=1);
namespace Web\Legislation\Action\Update;

use Application\Models\Legislation\Action;


class Controller extends \Web\Controller
{
    public function __invoke(array $params): View
    {
        $action = null;
        if (!empty($_REQUEST['legislationAction_id'])) {
            try {
                $action = new Action($_REQUEST['legislationAction_id']);
            } catch (\Exception $e) {
                $_SESSION['errorMessages'][] = $e;
            }
        } else {
            if (!empty($_REQUEST['legislation_id']) && !empty($_REQUEST['type_id'])) {
                try {
                    $action = new Action();
                    $action->setLegislation_id($_REQUEST['legislation_id']);
                    $action->setType_id($_REQUEST['type_id']);
                } catch (\Exception $e) {
                    $_SESSION['errorMessages'][] = $e;
                }
            }
        }

        if ($action && isset($_POST['legislation_id'])) {
            try {
                $action->handleUpdate($_POST);
                $action->save();

                $return_url = \Web\View::generateUrl('legislation.view') . '?legislation_id=' . $action->getLegislation_id();
                header("Location: $return_url");
                exit();
            } catch (\Exception $e) {
                $_SESSION['errorMessages'][] = $e;
            }
        }

        return new View($action);
    }
}
