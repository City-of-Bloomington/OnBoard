<?php
/**
 * @copyright 2014-2023 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare(strict_types=1);
namespace Web\Appointers\Update;

use Application\Models\Appointer;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): View
    {
        $appointer_id = $_REQUEST['id'] ?? null;

        if (!$appointer_id) {
            $_SESSION['errorMessages'][] = 'No appointer specified';
            header('Location: ' . \Web\View::generateUrl('appointers.index'));
            exit();
        }

        try {
            $appointer = new Appointer($appointer_id);
        } catch (\Exception $e) {
            $_SESSION['errorMessages'][] = $e->getMessage();
            header('Location: ' . \Web\View::generateUrl('appointers.index'));
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
            $appointer->setName($_POST['name']);
            try {
                $appointer->save();
                $_SESSION['successMessages'][] = 'Appointer successfully updated';
                $return_url = \Web\View::generateUrl('appointers.index');
                header("Location: $return_url");
                exit();
            } catch (\Exception $e) {
                $_SESSION['errorMessages'][] = $e->getMessage();
            }
        }

        return new View($appointer);
    }
}