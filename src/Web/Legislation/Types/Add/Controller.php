<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Legislation\Types\Add;

use Application\Models\Legislation\Type;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        $type = new Type();
        if (isset($_POST['name'])) {
            try {
                $type->handleUpdate($_POST);
                $type->save();
                header('Location: '.\Web\View::generateUrl('legislationTypes.index'));
                exit();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        return new \Web\Legislation\Types\Update\View($type);
    }
}
