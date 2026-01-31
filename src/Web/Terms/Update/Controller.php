<?php
/**
 * @copyright 2024-2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Terms\Update;

use Application\Models\CommitteeHistory;
use Application\Models\Seat;
use Application\Models\Term;
use Application\Models\TermTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_REQUEST['term_id'])) {
            try { $term = new Term($_REQUEST['term_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        if (isset($term)) {
            if (isset($_POST['seat_id'])) {
                $original = new Term($term->getId());

                try {
                    $term->setStartDate($_POST['startDate'], 'Y-m-d');
                    $term->setEndDate  ($_POST['endDate'  ], 'Y-m-d');
                    $term->save();

                    $change  = [CommitteeHistory::STATE_ORIGINAL => $original,
                                CommitteeHistory::STATE_UPDATED  => $term->getData()];
                    CommitteeHistory::saveNewEntry([
                        'committee_id' => $term->getSeat()->getCommittee_id(),
                        'tablename'    => 'terms',
                        'action'       => 'edit',
                        'changes'      => [$change]
                    ]);

                    $url = \Web\View::generateUrl('seats.view', ['seat_id'=>$term->getSeat_id()]);
                    header("Location: $url");
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
            }

            $seat = $term->getSeat();
            return new View($seat, $term);
        }

        return new \Web\Views\NotFoundView();
    }
}
