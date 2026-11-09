<?php
/**
 * @copyright 2024-2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Reports\Add;

use Application\Models\Reports\Report;
use Application\Models\Reports\ReportsTable;
use Application\Models\Committee;
use Application\Models\CommitteeTable;

use Web\Reports\Update\Controller as UpdateController;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_REQUEST['committee_id'])) {
            try {
                $c      = new Committee($_REQUEST['committee_id']);
                $report = new Report();
                $report->setCommittee($c);
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        if (!isset($report)) {
            return new \Web\Views\NotFoundView();
        }

        if (isset($_POST['committee_id'])) {
            try { UpdateController::saveAndRedirect($report); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }
        return new \Web\Reports\Update\View($report);

    }
}
