<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Committees\End;

use Application\Models\Committee;
use Application\Models\CommitteeTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $p): \Web\View
    {
        if (!empty($p['id'])) {
            try { $committee = new Committee($p['id']); }
            catch (\Exception $e) {
                $_SESSION['errorMessages'][] = $e->getMessage();
                return new \Web\Views\NotFoundView();
            }
        }

        if (isset($committee)) {
            if (isset($_POST['endDate'])) {
                try {
                    CommitteeTable::end($committee, new \DateTime($_POST['endDate']));

                    $url = \Web\View::generateUrl('committees.info', ['id'=>$committee->getId()]);
                    header("Location: $url");
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
            }

            return new View($committee);
        }
        return new \Web\Views\NotFoundView();
    }
}
