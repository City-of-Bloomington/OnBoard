<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Controllers;

use Application\Models\Committee;
use Application\Models\MeetingFile;
use Application\Models\MeetingFilesTable;

use Blossom\Classes\Controller;
use Blossom\Classes\Block;

class MeetingFilesController extends Controller
{
    public function index()
    {
        $search = [];

        if (!empty($_GET['committee_id'])) {
            try {
                $committee = new Committee($_GET['committee_id']);

                $this->template->title = $committee->getName();
                $this->template->blocks[] = new Block('committees/breadcrumbs.inc',  ['committee' => $committee]);
                $this->template->blocks[] = new Block('committees/header.inc',       ['committee' => $committee]);

                $search['committee_id'] = $committee->getId();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }

		$page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
		$sort = new \stdClass();
		$sort->field     = 'meetingDate';
		$sort->direction = 'desc';
		if (!empty($_GET['sort'])) {
            list($f, $d) = explode(' ', $_GET['sort']);
            if (in_array($f, MeetingFilesTable::$sortableFields)) {
                $sort->field     = $f;
                $sort->direction = $d == 'asc' ? 'asc' : 'desc';
            }
		}

		if (!empty($_GET['type'])) {
            if (in_array($_GET['type'], MeetingFilesTable::$types)) { $search['type'] = $_GET['type']; }
		}

        $table = new MeetingFilesTable();
        $list  = $table->find($search, "{$sort->field} {$sort->direction}", true);
		$list->setCurrentPageNumber($page);
		$list->setItemCountPerPage(20);

        $this->template->blocks[] = new Block('meetingFiles/list.inc', [
            'files'     => $list,
            'committee' => isset($committee) ? $committee : null,
            'sort'      => $sort
        ]);
        $this->template->blocks[] = new Block('pageNavigation.inc', ['paginator'=>$list]);
    }

    public function years()
    {
        $search = [];

        if (!empty($_GET['committee_id'])) {
            try {
                $committee = new Committee($_GET['committee_id']);

                if ($this->template->outputFormat == 'html') {
                    $this->template->title = $committee->getName();
                    $this->template->blocks[] = new Block('committees/breadcrumbs.inc',  ['committee' => $committee]);
                    $this->template->blocks[] = new Block('committees/header.inc',       ['committee' => $committee]);
                }

                $search['committee_id'] = $committee->getId();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }

        $table = new MeetingFilesTable();
        $years = $table->years($search);
        $this->template->blocks[] = new Block('meetingFiles/years.inc', [
            'years'    => $years,
            'committe' => isset($committee) ? $committee : null
        ]);
    }

    public function update()
    {
        if (!empty($_REQUEST['meetingFile_id'])) {
            try { $file = new MeetingFile($_REQUEST['meetingFile_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }
        else { $file = new MeetingFile(); }

        if (!$file->getCommittee_id()) {
            if (!empty($_REQUEST['committee_id'])) {
                try {
                    $c = new Committee($_REQUEST['committee_id']);
                    $file->setCommittee($c);
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
            }
        }

        if (!empty($_REQUEST['type'])) { $file->setType($_REQUEST['type']); }

        if (isset($file) && $file->getCommittee_id()) {
            if (isset($_POST['type'])) {
                try {
                    $file->handleUpdate(
                        $_POST,
                        (isset($_FILES['meetingFile']) && $_FILES['meetingFile']['error'] != UPLOAD_ERR_NO_FILE)
                             ? $_FILES['meetingFile']
                             : null
                    );
                    $file->save();

                    $return_url = !empty($_POST['return_url'])
                        ? $_POST['return_url']
                        : BASE_URL."/meetingFiles?committee_id={$file->getCommittee_id()}";
                    header("Location: $return_url");
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
            }

            $committee = $file->getCommittee();
            $this->template->title = $committee->getName();
            $this->template->blocks[] = new Block('committees/breadcrumbs.inc',  ['committee' => $committee]);
            $this->template->blocks[] = new Block('committees/header.inc',       ['committee' => $committee]);
            $this->template->blocks[] = new Block('meetingFiles/updateForm.inc', ['meetingFile' => $file]);
        }
        else {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
        }
    }

    public function download()
    {
        if (!empty($_GET['meetingFile_id'])) {
            try {
                $file = new MeetingFile($_GET['meetingFile_id']);
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
        if (!empty($_GET['meetingFile_id'])) {
            try {
                $file = new MeetingFile($_GET['meetingFile_id']);
                $committe = $file->getCommittee();
                $file->delete();
                header('Location: '.BASE_URI.'/meetingFiles?committee_id='.$committe->getId());
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
