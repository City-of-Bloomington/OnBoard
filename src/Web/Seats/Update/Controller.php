<?php
/**
 * @copyright 2024-2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Seats\Update;

use Application\Models\AppointerTable;
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
            if (isset($_POST['name'])) {
                $original = $seat->getData();
                try {
                    $seat->setCode             ($_POST['code'             ]);
                    $seat->setName             ($_POST['name'             ]);
                    $seat->setAppointer_id     ($_POST['appointer_id'     ]);
                    $seat->setStartDate        ($_POST['startDate'], 'Y-m-d');
                    $seat->setRequirements     ($_POST['requirements'     ]);
                    $seat->setType             ($_POST['type'             ]);
                    $seat->setTermLength       ($_POST['termLength'       ]);
                    $seat->setTermModifier     ($_POST['termModifier'     ]);
                    $seat->setVoting           ($_POST['voting'           ] ?? false);
                    $seat->setTakesApplications($_POST['takesApplications'] ?? false);

                    $seat->save();

                    $changes = [[CommitteeHistory::STATE_ORIGINAL => $original,
                                 CommitteeHistory::STATE_UPDATED  => $seat->getData()]];
                    CommitteeHistory::saveNewEntry([
                        'committee_id' => $seat->getCommittee_id(),
                        'tablename'    => 'seats',
                        'action'       => 'edit',
                        'changes'      => $changes
                    ]);

                    $url = View::generateUrl('seats.view', ['seat_id'=>$seat->getId()]);
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
