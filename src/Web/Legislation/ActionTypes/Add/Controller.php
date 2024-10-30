<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Legislation\ActionTypes\Add;

use Application\Models\Legislation\ActionType;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        $type = new ActionType();

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

        return new \Web\Legislation\ActionTypes\Update\View($type);
    }
}
