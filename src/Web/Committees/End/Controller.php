<?php
/**
 * @copyright 2024-2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Committees\End;

use Application\Models\Committee;
use Application\Models\CommitteeHistory;
use Application\Models\CommitteeTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $p): \Web\View
    {
        if (!empty($_REQUEST['committee_id'])) {
            try { $committee = new Committee($_REQUEST['committee_id']); }
            catch (\Exception $e) {
                $_SESSION['errorMessages'][] = $e->getMessage();
                return new \Web\Views\NotFoundView();
            }
        }

        if (isset($committee)) {
            if (isset($_POST['endDate'])) {
                try {
                    $change  = [CommitteeHistory::STATE_ORIGINAL => $committee->getData()];

                    $table   = new CommitteeTable();
                    $endDate = new \DateTime($_POST['endDate']);
                    $table->end($committee, $endDate);

                    $change[CommitteeHistory::STATE_UPDATED] = $committee->getData();
                    CommitteeHistory::saveNewEntry([
                        'committee_id'=> $committee->getId(),
                        'tablename'   => 'committees',
                        'action'      => 'end',
                        'changes'     => [$change]
                    ]);

                    $url = \Web\View::generateUrl('committees.info', ['committee_id'=>$committee->getId()]);
                    header("Location: $url");
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
            }

            return new View($committee);
        }
        return new \Web\Views\NotFoundView();
    }
}
