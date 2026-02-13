<?php
/**
 * @copyright 2024-2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Seats\End;

use Application\Models\CommitteeHistory;
use Application\Models\Seat;
use Application\Models\SeatTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_REQUEST['seat_id'])) {
            try { $seat = new Seat($_REQUEST['seat_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        if (isset($seat)) {
            if (isset($_POST['endDate'])) {
                try {
                    $endDate = new \DateTime($_POST['endDate']);

                    $change[CommitteeHistory::STATE_ORIGINAL] = $seat->getData();
                    $seat->saveEndDate($endDate);
                    $change[CommitteeHistory::STATE_UPDATED ] = $seat->getData();

                    CommitteeHistory::saveNewEntry([
                        'committee_id' =>$seat->getCommittee_id(),
                        'tablename'    =>'seats',
                        'action'       =>'end',
                        'changes'      =>[$change]
                    ]);

                    $url = \Web\View::generateUrl('seats.view', ['seat_id'=>$seat->getId()]);
                    header("Location: $url");
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
            }

            return new View($seat);
        }

        return new \Web\Views\NotFoundView();
    }
}
