<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Reports\Update;

use Application\Models\Reports\Report;
use Application\Models\Reports\ReportsTable;
use Application\Models\Committee;
use Application\Models\CommitteeTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($params['id'])) {
            try { $report = new Report($params['id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
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
                    $return_url = \Web\View::generateUrl('reports.index').'?committee_id='.$report->getCommittee_id();
                    header("Location: $return_url");
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
            }

            return new View($report);
        }

        return new \Web\Views\NotFoundView();
    }

    /**
     * ACL will call this function before invoking the Controller
     *
     * When a role needs to check the Department Association, the ACL will
     * be checked before invoking the Controller.  This function must be called
     * statically.  The current route parameters will be passed.  These parameters
     * will be the same as would be passed to __invoke().
     *
     * @see Web\Auth\DepartmentAssociation
     * @see access_control.php
     */
    public static function hasDepartment(int $department_id, array $params): bool
    {
        if (!empty($params['id'])) {
            return ReportsTable::hasDepartment($department_id, (int)$params['id']);
        }

        return false;
    }
}
