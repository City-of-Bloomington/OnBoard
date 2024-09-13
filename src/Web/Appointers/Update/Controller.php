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
        $return_url = View::generateUrl('appointers.index');

        if (!empty($_REQUEST['appointer_id'])) {
            try { $appointer = new Appointer($_REQUEST['appointer_id']); }
            catch (\Exception $e) {
                $_SESSION['errorMessages'][] = $e->getMessage();
                header("Location: $return_url");
                exit();
            }
        }
        else { $appointer = new Appointer(); }

        if (isset($_POST['name'])) {
            $appointer->setName($_POST['name']);
            try {
                $appointer->save();
                header("Location: $return_url");
                exit();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        return new View($appointer);
    }
}
