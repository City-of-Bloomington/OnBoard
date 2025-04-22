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
        if (!empty($params['id'])) {
            try { $member = new Member($params['id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        if (isset($member)) {
            if (!empty($_POST['committee_id'])) {
                $member->setPerson_id($_POST['person_id']);
                $member->setStartDate($_POST['startDate']);
                $member->setEndDate(!empty($_POST['endDate']) ? $_POST['endDate'] : null);

                try {
                    MemberTable::update($member);

                    $url = $member->getSeat_id()
                           ? View::generateUrl(     'seats.view'   , ['id'=>$member->getSeat_id()      ])
                           : View::generateUrl('committees.members', ['id'=>$member->getCommittee_id() ]);
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
