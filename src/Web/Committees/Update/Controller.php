<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
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
        if (!empty($param['id'])) {
            try { $committee = new Committee($param['id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        if (isset($committee)) {
            if (isset($_POST['name'])) {
                try {
                    CommitteeTable::update($committee, $_POST);
                    $url = \Web\View::generateUrl('committees.info', ['id'=>$committee->getId()]);
                    header("Location: $url");
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
            }

            return new View($committee);
        }

    }
}
