<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Committees\Statutes\Add;

use Application\Models\Committee;
use Application\Models\CommitteeStatute;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_REQUEST['committee_id'])) {
            try {
                $committee = new Committee($_REQUEST['committee_id']);
                $statute   = new CommitteeStatute();
                $statute->setCommittee($committee);
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        if (isset($statute)) {
            if (isset($_POST['committee_id'])) {
                try {
                    $statute->handleUpdate($_POST);
                    $statute->save();
                    $return_url = \Web\View::generateUrl('committees.statutes', ['id'=>$statute->getCommittee_id()]);
                    header("Location: $return_url");
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
            }
            return new \Web\Committees\Statutes\Update\View($statute);
        }

        return new \Web\Views\NotFoundView();
    }
}
