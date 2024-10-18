<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Members\Appoint;

use Application\Models\Member;
use Application\Models\MemberTable;
use Application\Models\Term;
use Application\Models\Seat;
use Application\Models\Committee;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        try {
            if (    !empty($_REQUEST['term_id'     ])) { $o = new Term     ($_REQUEST['term_id']); }
            elseif (!empty($_REQUEST['seat_id'     ])) { $o = new Seat     ($_REQUEST['seat_id']); }
            elseif (!empty($_REQUEST['committee_id'])) { $o = new Committee($_REQUEST['committee_id']); }
            elseif (!empty($_REQUEST['newMember']['term_id'     ])) { $o = new Term     ($_REQUEST['newMember']['term_id']); }
            elseif (!empty($_REQUEST['newMember']['seat_id'     ])) { $o = new Seat     ($_REQUEST['newMember']['seat_id']); }
            elseif (!empty($_REQUEST['newMember']['committee_id'])) { $o = new Committee($_REQUEST['newMember']['committee_id']); }
            $newMember = $o->newMember();

            $seat = $newMember->getSeat();
            if ($seat) {
                // If the current member has already been closed out,
                // there's no reason to include them in the form
                $currentMember = $seat->getLatestMember();
                if ($currentMember && $currentMember->getEndDate()) { unset($currentMember); }
            }

            if (isset($_REQUEST['newMember']['person_id'])) { $newMember->setPerson_id($_POST['newMember']['person_id']); }
            if (isset($_REQUEST['newMember']['startDate'])) { $newMember->setStartDate($_POST['newMember']['startDate'], 'Y-m-d'); }
        }
        catch (\Exception $e) {
            $_SESSION['errorMessages'][] = $e->getMessage();
            return \Web\Views\NotFoundView();
        }

        if (isset($_POST['newMember'])) {
            try {
                $endDate = !empty($_POST['currentMember']['endDate']) ? new \DateTime($_POST['currentMember']['endDate']) : null;

                MemberTable::appoint($newMember, $endDate);

                $return_url = \Web\View::generateUrl('committees.members', ['id'=>$newMember->getCommittee_id()]);
                header("Location: $return_url");
                exit();
            }
            catch (\Exception $e) {
                $_SESSION['errorMessages'][] = $e->getMessage();
            }
        }

        return new View($newMember, $currentMember ?? null);
    }
}
