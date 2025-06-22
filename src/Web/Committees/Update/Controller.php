<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Committees\Update;

use Application\Models\Committee;
use Application\Models\CommitteeTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_REQUEST['committee_id'])) {
            try { $c = new Committee($_REQUEST['committee_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        if (isset($c)) {
            if (isset($_POST['name'])) {
                try {
                    CommitteeTable::update($c, $_POST);
                    $url = \Web\View::generateUrl('committees.info', ['committee_id'=>$c->getId()]);
                    header("Location: $url");
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
            }

            return new View($c);
        }
        return new \Web\Views\NotFoundView();
    }
}
