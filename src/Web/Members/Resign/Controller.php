<?php
/**
 * @copyright 2024-2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Members\Resign;

use Application\Models\CommitteeHistory;
use Application\Models\Member;
use Application\Models\MemberTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_REQUEST['member_id'])) {
            try { $member = new Member($_REQUEST['member_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        if (isset($member)) {
            if (!empty($_POST['endDate'])) {
                $original   = $member->getData();
                $return_url = \Web\View::generateUrl('committees.members', ['committee_id'=>$member->getCommittee_id()]);

                try {
                    $endDate  = new \DateTime($_POST['endDate']);
                    $member->setEndDate($endDate->format('Y-m-d'));
                    $member->save();
                    $updated  = $member->getData();

                    CommitteeHistory::saveNewEntry([
                        'committee_id' => $member->getCommittee_id(),
                        'tablename'    => 'members',
                        'action'       => 'resign',
                        'changes'      => [['original'=>$original, 'updated'=>$updated]]
                    ]);

                    header("Location: $return_url");
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }

            }
            return new View($member);
        }

        return new \Web\Views\NotFoundView();
    }
}
