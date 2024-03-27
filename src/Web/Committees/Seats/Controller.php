<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Committees\Seats;

use Application\Models\Committee;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_GET['committee_id'])) {
            try {
                $committee = new Committee($_GET['committee_id']);
                $seats     = [];
                foreach ($committee->getSeats() as $s) {
                    $seats[] = $s;
                }
                return new View($committee, $seats);
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }

        return new \Web\Views\NotFoundView();
    }
}
