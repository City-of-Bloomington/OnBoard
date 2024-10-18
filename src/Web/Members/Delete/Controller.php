<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Members\Delete;

use Application\Models\Member;
use Application\Models\MemberTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        try {
            if (!empty($_REQUEST['member_id'])) {
                $m  = new Member($_REQUEST['member_id']);

                $url = $m->getSeat_id()
                        ? \Web\View::generateUrl('seats.view')."?seat_id={$m->getSeat_id()}"
                        : \Web\View::generateUrl('committees.members', ['id'=>$m->getCommittee_id()]);

                MemberTable::delete($m);
                header("Location: $url");
                exit();
            }
        }
        catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }

        header('Location: '.\Web\View::generateUrl('committees.index'));
        exit();
    }
}
