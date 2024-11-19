<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Appointers\Add;

use Application\Models\Appointer;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        $appointer  = new Appointer();
        $return_url = \Web\View::generateUrl('appointers.index');

        if (isset($_POST['name'])) {
            $appointer->setName($_POST['name']);
            try {
                $appointer->save();
                header("Location: $return_url");
                exit();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        return new \Web\Appointers\Update\View($appointer);
    }
}
