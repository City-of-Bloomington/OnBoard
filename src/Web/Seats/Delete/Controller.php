<?php
/**
 * @copyright 2024-2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Seats\Delete;

use Application\Models\CommitteeHistory;
use Application\Models\Seat;
use Application\Models\SeatTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_REQUEST['seat_id'])) {
            try {
                $seat   = new Seat($_REQUEST['seat_id']);
                $cid    = $seat->getCommittee_id();
                $change = [CommitteeHistory::STATE_ORIGINAL=>$seat->getData()];
                $seat->delete();

                CommitteeHistory::saveNewEntry([
                    'committee_id' => $cid,
                    'tablename'    => 'seats',
                    'action'       => 'delete',
                    'changes'      => [$change]
                ]);

                $url = \Web\View::generateUrl('committees.members', ['committee_id'=>$cid]);
                header("Location: $url");
                exit();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }
        header('Location: '.\Web\View::generateUrl('committees.index'));
        exit();
    }
}
