<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Committees\Statutes\Delete;

use Application\Models\CommitteeStatute;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_REQUEST['committeeStatute_id'])) {
            try { $statute = new CommitteeStatute($_REQUEST['committeeStatute_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        if (isset($statute)) {
            $return_url = \Web\View::generateUrl('committees.statutes', ['committee_id'=>$statute->getCommittee_id()]);

            try { $statute->delete(); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }

            header("Location: $return_url");
            exit();
        }

        return new \Web\Views\NotFoundView();
    }
}
