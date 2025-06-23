<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Races\Update;

use Application\Models\Race;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_REQUEST['race_id'])) {
            try { $race = new Race($_REQUEST['race_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        if (isset($race)) {
            if (isset($_POST['name'])) {
                $race->setName($_POST['name']);
                try {
                    $race->save();
                    header('Location: '.\Web\View::generateUrl('races.index'));
                    exit();
                }
                catch (\Exception $e) {
                    $_SESSION['errorMessages'][] = $e->getMessage();
                }
            }
            return new View($race);
        }

        return new \Web\Views\NotFoundView();
    }
}
