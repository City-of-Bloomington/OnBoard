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
        if (!empty($params['id'])) {
            try {
                $s   = new Seat($params['id']);
                $url = \Web\View::generateUrl('committees.members', ['id'=>$s->getCommittee_id()]);

                SeatTable::delete($s);
                header("Location: $url");
                exit();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }
        header('Location: '.\Web\View::generateUrl('committees.index'));
        exit();
    }
}
