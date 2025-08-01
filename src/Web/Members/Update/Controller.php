<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
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
        if (!empty($_REQUEST['member_id'])) {
            try { $member = new Member($_REQUEST['member_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        if (empty($_SESSION['return_url'])) { $_SESSION['return_url'] = self::return_url($member); }

        if (isset($member)) {
            if (!empty($_POST['committee_id'])) {
                $member->setPerson_id($_POST['person_id']);
                $member->setStartDate($_POST['startDate']);
                $member->setEndDate(!empty($_POST['endDate']) ? $_POST['endDate'] : null);

                try {
                    MemberTable::update($member);

                    $url = $_SESSION['return_url'];
                    unset ($_SESSION['return_url']);
                    header("Location: $url");
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
            }
            return new View($member, $_SESSION['return_url']);
        }
        return new \Web\Views\NotFoundView();
    }

    private static function return_url(Member $m): string
    {
        return !empty($_REQUEST['return_url'])
                    ? $_REQUEST['return_url']
                    : ($m->getSeat_id()
                          ? \Web\View::generateUrl('seats.view', ['seat_id'=>$m->getSeat_id()])
                          : \Web\View::generateUrl('committees.members', ['committee_id'=>$m->getCommittee_id()]));
    }
}
