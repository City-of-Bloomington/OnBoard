<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Races\Update;

use Application\Models\Race;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        $return_url = View::generateUrl('races.index');
        if (!empty($_REQUEST['race_id'])) {
            try { $race = new Race($_REQUEST['race_id']); }
            catch (\Exception $e) {
                $_SESSION['errorMessages'][] = $e->getMessage();
                header("Location: $return_url");
                exit();
            }
        }
        else {
            $race = new Race();
        }

        if (isset($_POST['name'])) {
            $race->setName($_POST['name']);
            try {
                $race->save();
                header("Location: $return_url");
                exit();
            }
            catch (\Exception $e) {
                $_SESSION['errorMessages'][] = $e->getMessage();
            }
        }

        return new View($race);
    }
}
