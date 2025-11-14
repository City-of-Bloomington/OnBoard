<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Committees\Seats;

use Application\Models\Committee;

class Controller extends \Web\Controller
{
    public function __invoke(array $p): \Web\View
    {
        if (!empty($_REQUEST['committee_id'])) {
            try {
                $committee = new Committee($_REQUEST['committee_id']);
                $seats     = [];
                $search    = [
                    'current' => isset($_GET['current']) ? (bool)$_GET['current'] : true
                ];

                foreach ($committee->getSeats($search) as $s) {
                    $seats[] = $s;
                }
                return new View($committee, $seats, $search);
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        return new \Web\Views\NotFoundView();
    }
}
