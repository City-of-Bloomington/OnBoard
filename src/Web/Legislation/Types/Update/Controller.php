<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Legislation\Types\Update;

use Application\Models\Legislation\Type;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($params['id'])) {
            try { $type = new Type($params['id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        if (isset($type)) {
            if (isset($_POST['name'])) {
                try {
                    $type->handleUpdate($_POST);
                    $type->save();
                    header('Location: '.\Web\View::generateUrl('legislationTypes.index'));
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
            }
            return new View($type);
        }

        return new \Web\Views\NotFoundView();
    }
}
