<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Liaisons\Delete;

use Application\Models\Liaison;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_REQUEST['liaison_id'])) {
            try {
                $liaison      = new Liaison($_REQUEST['liaison_id']);
                $committee_id = $liaison->getCommittee_id();

                try { $liaison->delete(); }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }

                $return_url = \Web\View::generateUrl('committees.liaisons', ['committee_id'=>$committee_id]);
                header("Location: $return_url");
                exit();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        return new \Web\Views\NotFoundView();
    }
}
