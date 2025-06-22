<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Liaisons\Add;

use Application\Models\Committee;
use Application\Models\Liaison;
use Application\Models\Person;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {

        if (!empty($_REQUEST['committee_id'])) {
            try {
                $committee = new Committee($_REQUEST['committee_id']);
                $liaison   = new Liaison();
                $liaison->setCommittee($committee);
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

            return new \Web\Liaisons\Update\View($liaison);
        }

        return new \Web\Views\NotFoundView();
    }
}
