<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Terms\Add;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_REQUEST['seat_id'])) {
            try {
                $seat = new Seat($_REQUEST['seat_id']);
                $term = new Term();
                $term->setSeat($seat);
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        if (isset($term)) {
            if (isset($_POST['seat_id'])) {
                try {
                    $term->setStartDate($_POST['startDate'], 'Y-m-d');
                    $term->setEndDate  ($_POST['endDate'  ], 'Y-m-d');
                    TermTable::update($term);
                    $return_url = \Web\View::generateUrl('seats.view').'?seat_id='.$term->getSeat_id();
                    header("Location: $return_url");
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
            }

            $seat = $term->getSeat();
            return new \Web\Terms\Update\View($seat, $term);
        }

        return new \Web\Views\NotFoundView();
    }
}
