<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Committees\Statutes\Delete;

use Application\Models\CommitteeStatute;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_GET['id'])) {
            try { $statute = new CommitteeStatute($_GET['id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        if (isset($statute)) {
            try {
                $committee_id = $statute->getCommittee_id();
                $statute->delete();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }

            $return_url = \Web\View::generateUrl('committees.info').'?committee_id='.$committee_id;
            header("Location: $return_url");
            exit();
        }

        return new \Web\Views\NotFoundView();
    }
}
