<?php
/**
 * @copyright 2024-2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Committees\Add;

use Application\Models\Committee;
use Application\Models\CommitteeHistory;
use Application\Models\CommitteeTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $p): \Web\View
    {
        $c = new Committee();

        if (isset($_POST['name'])) {
            try {
                $c->handleUpdate($_POST);
                $c->save();
                $change = [CommitteeHistory::STATE_UPDATED => $c->getData()];

                CommitteeHistory::saveNewEntry([
                    'committee_id'=> $c->getId(),
                    'tablename'   => 'committees',
                    'action'      => 'add',
                    'changes'     => [$change]
                ]);

                $url = \Web\View::generateUrl('committees.info', ['committee_id'=>$c->getId()]);
                header("Location: $url");
                exit();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }
        return new View($c);
    }
}
