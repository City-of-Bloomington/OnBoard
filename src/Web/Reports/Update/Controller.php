<?php
/**
 * @copyright 2024-2026 City of Bloomington, Indiana
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
        if (!empty($_REQUEST['report_id'])) {
            try { $report = new Report($_REQUEST['report_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        if (isset($report) && $report->getCommittee_id()) {

            if (isset($_POST['committee_id'])) {
                try { self::saveAndRedirect($report); }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
            }

            return new View($report);
        }

        return new \Web\Views\NotFoundView();
    }

    /**
     * @throws \Exception
     */
    public static function saveAndRedirect(Report $r)
    {

        $r->setCommittee_id($_POST['committee_id']);
        $r->setTitle       ($_POST['title'       ]);
        $r->setReportDate  ($_POST['reportDate'  ], 'Y-m-d');
        $r->setUpdatedPerson($_SESSION['USER']);

        $r->validateDatabaseInformation();

        $file = (isset($_FILES['reportFile']) && $_FILES['reportFile']['error'] != UPLOAD_ERR_NO_FILE)
                ? $_FILES['reportFile']
                : null;
        if ($file) {
            $r->setFile($file);
            $r->setUrl(null);
        }
        else {
            $r->setUrl($_POST['url']);
        }

        $r->save();
        $url = \Web\View::generateUrl('reports.index').'?committee_id='.$r->getCommittee_id();
        header("Location: $url");
        exit();
    }
}
