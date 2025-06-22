<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Members\Reappoint;

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
            $seat = $member->getSeat();
            if ($seat && $seat->getType() == 'termed') {

                if (!empty($_POST['confirm']) && $_POST['confirm']=='yes') {
                    try {
                        MemberTable::reappoint($member);
                        $return_url = \Web\View::generateUrl('committees.members', ['committee_id'=>$member->getCommittee_id()]);
                        header('Location: '.$return_url);
                        exit();
                    }
                    catch (\Exception $e) {
                        $_SESSION['errorMessages'][] = $e->getMessage();
                    }
                }

                return new View($member);
            }
        }

        return new \Web\Views\NotFoundView();
    }
}
