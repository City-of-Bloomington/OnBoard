<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Reports\Delete;

use Application\Models\Reports\Report;
use Application\Models\Reports\ReportsTable;
use Application\Models\CommitteeTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_REQUEST['report_id'])) {
            try {
                $file         = new Report($_REQUEST['report_id']);
                $committee_id = $file->getCommittee_id();
                $return_url   = \Web\View::generateUrl('reports.index').'?committee_id='.$committee_id;

                $file->delete();

                header("Location: $return_url");
                exit();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        return new \Web\Views\NotFoundView();
    }
}
