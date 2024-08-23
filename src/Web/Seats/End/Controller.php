<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Seats\End;

use Application\Models\Seat;
use Application\Models\SeatTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_REQUEST['seat_id'])) {
            try { $seat = new Seat($_REQUEST['seat_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }

        if (isset($seat)) {
            if (isset($_POST['endDate'])) {
                try {
                    SeatTable::end($seat, new \DateTime($_POST['endDate']));
                    $return_url = \Web\View::generateUrl('seats.view').'?seat_id='.$seat->getId();
                    header("Location: $return_url");
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
            }

            return new View($seat);
        }

        return new \Web\Views\NotFoundView();
    }
}
