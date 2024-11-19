<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare(strict_types=1);
namespace Web\Legislation\ActionTypes\Update;

use Application\Models\Legislation\ActionType;


class Controller extends \Web\Controller
{
    public function __invoke(array $params): View
    {
        if (!empty($params['id'])) {
            try { $type = new ActionType($params['id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        if (isset($type)) {
            if (isset($_POST['name'])) {
                try {
                    $type->handleUpdate($_POST);
                    $type->save();
                    $return_url = \Web\View::generateUrl('legislationActionTypes.index');
                    header("Location: $return_url");
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
            }

            return new View($type);
        }

        return new \Web\Views\NotFoundView();
    }
}
