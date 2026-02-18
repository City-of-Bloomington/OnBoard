<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Reports\Add;

use Application\Models\Reports\Report;
use Application\Models\Reports\ReportsTable;
use Application\Models\Committee;
use Application\Models\CommitteeTable;

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
            $file = (isset($_FILES['reportFile']) && $_FILES['reportFile']['error'] != UPLOAD_ERR_NO_FILE)
                    ? $_FILES['reportFile']
                    : null;

            try {
                $report->setCommittee_id($_POST['committee_id']);
                $report->setTitle       ($_POST['title'       ]);
                $report->setReportDate  ($_POST['reportDate'  ], 'Y-m-d');
                $report->setUpdatedPerson($_SESSION['USER']);

                // Before we save the file, make sure all the database information is correct
                $report->validateDatabaseInformation();
                // If they are editing an existing document, they do not need to upload a new file
                if ($file) {$report->setFile($file); }

                $report->save();
                $return_url = \Web\View::generateUrl('reports.index').'?committee_id='.$report->getCommittee_id();
                header("Location: $return_url");
                exit();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }
        return new \Web\Reports\Update\View($report);

    }
}
