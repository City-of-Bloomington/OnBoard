<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Members\Update;

use Application\Models\Committee;
use Application\Models\Member;
use Application\Models\MemberTable;
use Application\Models\Seat;
use Application\Models\Term;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        try {
            if (!empty($_REQUEST['member_id'])) { $member = new Member($_REQUEST['member_id']); }
            else {
                if     (!empty($_REQUEST['term_id'     ])) { $o = new Term($_REQUEST['term_id']); }
                elseif (!empty($_REQUEST['seat_id'     ])) { $o = new Seat($_REQUEST['seat_id']); }
                elseif (!empty($_REQUEST['committee_id'])) { $o = new Committee($_REQUEST['committee_id']); }
                $member = $o->newMember();
            }
            if (!empty($_REQUEST['person_id'])) { $member->setPerson_id($_REQUEST['person_id']); }
            if (!empty($_REQUEST['startDate'])) { $member->setStartDate($_REQUEST['startDate'], 'Y-m-d'); }
        }
        catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }

        if (isset($member)) {
            if (!empty($_POST['committee_id'])) {
                try {
                    if (!empty($_POST['endDate'])) {
                           $member->setEndDate($_POST['endDate'], 'Y-m-d');
                    }
                    else { $member->setEndDate(null); }

                    MemberTable::update($member);

                    $url = $member->getSeat_id()
                           ? View::generateUrl('seats.view').'?seat_id='.$member->getSeat_id()
                           : View::generateUrl('committees.members', ['id'=>$member->getCommittee_id()]);
                    header("Location: $url");
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
            }
            return new View($member);
        }
        return new \Web\Views\NotFoundView();
    }
}
