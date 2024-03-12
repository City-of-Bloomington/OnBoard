<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Committees\Members;

use Application\Models\Committee;
use Application\Models\SeatTable;
use Web\View;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): View
    {
        if (!empty($_GET['committee_id'])) {
            try { $committee = new Committee($_GET['committee_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }

        if (isset($committee)) {

            if ($committee->getType() === 'seated') {
                $data      = SeatTable::currentData(['committee_id'=>$committee->getId()]);
                $seat_data = [];
                foreach ($data['results'] as $row) {
                    $seat_data[] = $row;
                }
                return new SeatedView($committee, $seat_data);
            }
            else {
                $search = ['current' => true];
                if (isset($_GET['current']) && !$_GET['current']) {
                    $search['current'] = false;
                }

                $members = $committee->getMembers($search);
                return new OpenView($committee, $members, $current);
            }
        }
        return new \Web\View\NotFoundView();
    }
}
