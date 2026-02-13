<?php
/**
 * @copyright 2024-2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Members\Delete;

use Application\Models\CommitteeHistory;
use Application\Models\Member;
use Application\Models\MemberTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        try {
            if (!empty($_REQUEST['member_id'])) {
                $m   = new Member($_REQUEST['member_id']);
                $url = $m->getSeat_id()
                        ? \Web\View::generateUrl(     'seats.view'   , ['seat_id'     =>$m->getSeat_id()     ])
                        : \Web\View::generateUrl('committees.members', ['committee_id'=>$m->getCommittee_id()]);

                $changes = [['original'=>$m->getData()]];
                $m->delete();

                CommitteeHistory::saveNewEntry([
                    'committee_id' => $m->getCommittee_id(),
                    'tablename'    => 'members',
                    'action'       => 'delete',
                    'changes'      => $changes
                ]);
                header("Location: $url");
                exit();
            }
        }
        catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }

        header('Location: '.\Web\View::generateUrl('committees.index'));
        exit();
    }
}
