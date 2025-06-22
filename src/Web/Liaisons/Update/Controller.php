<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Liaisons\Update;

use Application\Models\Committee;
use Application\Models\Liaison;
use Application\Models\Person;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty(($params['id']))) { // ($params['id']
            try {
                $liaison = new Liaison(($params['id']));
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        if (isset($liaison)) {
            if (isset($_POST['person_id'])) {
                try {
                    $liaison->handleUpdate($_POST);
                    $liaison->save();

                    $return_url = \Web\View::generateUrl('committees.liaisons', ['committee_id'=>$liaison->getCommittee_id()]);

                    header("Location: $return_url");
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
            }

            return new View($liaison);
        }
        return new \Web\Views\NotFoundView();
    }
}
