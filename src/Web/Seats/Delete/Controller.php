<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Seats\Delete;

use Application\Models\Seat;
use Application\Models\SeatTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_REQUEST['seat_id'])) {
            try {
                $seat         = new Seat($_REQUEST['seat_id']);
                $committee_id = $seat->getCommittee_id();
                SeatTable::delete($seat);
                $return_url = \Web\View::generateUrl('committees.members')."?committee_id=$committee_id";
                header("Location: $return_url");
                exit();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }
        header('Location: '.\Web\View::generateUrl('committees.index'));
        exit();
    }
}
