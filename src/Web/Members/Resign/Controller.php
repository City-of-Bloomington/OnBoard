<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Members\Resign;

use Application\Models\Member;
use Application\Models\MemberTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($params['id'])) {
            try { $member = new Member($params['id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        if (isset($member)) {
            if (!empty($_POST['endDate'])) {
                try {
                    $endDate    = new \DateTime($_POST['endDate']);
                    $return_url = \Web\View::generateUrl('committees.members', ['id'=>$member->getCommittee_id()]);

                    MemberTable::resign($member, $endDate);

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
