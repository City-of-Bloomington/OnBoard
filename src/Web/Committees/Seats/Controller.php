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
    public function __invoke(array $p): \Web\View
    {
        if (!empty($p['id'])) {
            try {
                $committee = new Committee($p['id']);
                $seats     = [];
                $current   = isset($_GET['current']) ? (bool)$_GET['current'] : true;

                foreach ($committee->getSeats(['current'=>$current]) as $s) {
                    $seats[] = $s;
                }
                return new View($committee, $seats);
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        return new \Web\Views\NotFoundView();
    }
}
