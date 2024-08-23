<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Seats\Add;

use Application\Models\AppointerTable;
use Application\Models\Committee;
use Application\Models\Seat;
use Application\Models\SeatTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_REQUEST['committee_id'])) {
            try {
                $committee = new Committee($_REQUEST['committee_id']);
                $seat      = new Seat();
                $seat->setCommittee($committee);
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }

        if (isset($seat)) {
            if (isset($_POST['committee_id'])) {
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

                    SeatTable::update($seat);
                    $return_url = View::generateUrl('seats.view')."?seat_id={$seat->getId()}";
                    header("Location: $return_url");
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
            }

            $appointers = [];
            $table      = new AppointerTable();
            $list       = $table->find();
            foreach ($list as $a) { $appointers[] = ['id'=>$a->getId(), 'name'=>$a->getName()]; }

            return new View($seat, $appointers);

        }
        return new \Web\Views\NotFoundView();
    }
}
