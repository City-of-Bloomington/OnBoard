<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Application\Controllers;

use Application\Models\Committee;
use Application\Models\Reports\Report;
use Application\Models\Reports\ReportsTable;

use Blossom\Classes\Controller;
use Blossom\Classes\Block;

class ReportsController extends Controller
{
    public function index()
    {
        if (!empty($_GET['committee_id'])) {
            try { $committee = new Committee($_GET['committee_id']); }
            catch (\Exception $e) {
                $_SESSION['errorMesssages'][] = $e;
                unset($_GET['committee_id']);
            }
        }

        $table = new ReportsTable();
        if ($this->template->outputFormat == 'html') {
            $page  = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
            $list  = $table->find($_GET, 'reportDate desc', true);
            $list->setCurrentPageNumber($page);
            $list->setItemCountPerPage(20);
            $vars  = ['list'=>$list];

            if (isset($committee)) {
                $this->template->title = $committee->getName();
                $this->template->blocks[] = new Block('committees/breadcrumbs.inc', ['committee' => $committee]);
                $this->template->blocks[] = new Block('committees/header.inc',      ['committee' => $committee]);
                $vars['committee'] = $committee;
            }
            $this->template->blocks[] = new Block('reports/list.inc',   $vars);
            $this->template->blocks[] = new Block('pageNavigation.inc', ['paginator' => $list]);
        }
        else {
            $this->template->blocks[] = new Block('reports/list.inc', [
                'list' => $table->find($_GET)
            ]);
        }
    }

    public function update()
    {
        if (!empty($_REQUEST['id'])) {
            try { $report = new Report($_REQUEST['id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }
        else { $report = new Report(); }

        if (!$report->getCommittee_id()) {
            if (!empty($_REQUEST['committee_id'])) {
                try {
                    $c = new Committee($_REQUEST['committee_id']);
                    $report->setCommittee($c);
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
            }
        }

        if (isset($report) && $report->getCommittee_id()) {
            if (isset($_POST['committee_id'])) {
                $file = (isset($_FILES['reportFile']) && $_FILES['reportFile']['error'] != UPLOAD_ERR_NO_FILE)
                      ? $_FILES['reportFile']
                      : null;

                try {
                    $report->handleUpdate($_POST, $file);
                    $report->save();

                    header('Location: '.BASE_URL.'/reports?committee_id='.$report->getCommittee_id());
                    exit();
                }
                catch (\Exception $e) {
                    #echo "{$e->getMessage()}\n";
                    #print_r($report);
                    #exit();
                    $_SESSION['errorMessages'][] = $e;
                }
            }

            $this->template->blocks[] = new Block('committees/breadcrumbs.inc', ['committee' => $report->getCommittee()]);
            $this->template->blocks[] = new Block('committees/header.inc',      ['committee' => $report->getCommittee()]);
            $this->template->blocks[] = new Block('reports/updateForm.inc',     ['report'    => $report]);
        }
        else {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
        }
    }

    public function download()
    {
        if (!empty($_GET['id'])) {
            try {
                $file = new Report($_GET['id']);
                $this->template->setFilename('file');
                $this->template->blocks[] = new Block('files/download.inc', ['downloadFile'=>$file]);
            }
            catch (\Exception $e) {
                header('HTTP/1.1 404 Not Found', true, 404);
                $this->template->blocks[] = new Block('404.inc');
            }
        }
        else {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
        }
    }

    public function delete()
    {
        if (!empty($_GET['id'])) {
            try {
                $file = new Report($_GET['id']);
                $committee_id = $file->getCommittee_id();
                $file->delete();
                header('Location: '.BASE_URL.'/reports?committee_id='.$committee_id);
                exit();
            }
            catch (\Exception $e) {
                header('HTTP/1.1 404 Not Found', true, 404);
                $this->template->blocks[] = new Block('404.inc');
            }
        }
        else {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
        }
    }
}
