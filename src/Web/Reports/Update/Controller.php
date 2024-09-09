<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Reports\Update;

use Application\Models\Reports\Report;
use Application\Models\Committee;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_REQUEST['report_id'])) {
            try { $report = new Report($_REQUEST['report_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }
        else { $report = new Report(); }

        if (!$report->getCommittee_id()) {
            if (!empty($_REQUEST['committee_id'])) {
                try {
                    $c = new Committee($_REQUEST['committee_id']);
                    $report->setCommittee($c);
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
            }
        }

        if (isset($report) && $report->getCommittee_id()) {
            if (isset($_POST['committee_id'])) {
                $file = (isset($_FILES['reportFile']) && $_FILES['reportFile']['error'] != UPLOAD_ERR_NO_FILE)
                        ? $_FILES['reportFile']
                        : null;

                try {
                    $report->setCommittee_id($_POST['committee_id']);
                    $report->setTitle       ($_POST['title'       ]);
                    $report->setReportDate  ($_POST['reportDate'  ], 'Y-m-d');
                    // Before we save the file, make sure all the database information is correct
                    $report->validateDatabaseInformation();
                    // If they are editing an existing document, they do not need to upload a new file
                    if ($file) { $report->setFile($file); }

                    $report->save();
                    $return_url = View::generateUrl('reports.index').'?committee_id='.$report->getCommittee_id();
                    header("Location: $return_url");
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
            }

            return new View($report);
        }

        return new \Web\Views\NotFoundView();
    }
}
