<?php
/**
 * @copyright 2017-2021 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Controllers;

use Application\Models\Committee;
use Application\Models\MeetingFile;
use Application\Models\MeetingFilesTable;

use Web\Controller;
use Web\Block;
use Web\View;

class MeetingFilesController extends Controller
{
    public function index(): View
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
		if (!empty($_GET['year'])) {
            $search['year'] = (int)$_GET['year'];
		}

        $table = new MeetingFilesTable();
        if ($this->template->outputFormat != 'csv') {
            $list  = $table->find($search, "{$sort->field} {$sort->direction}", true);
            $list->setCurrentPageNumber($page);
            $list->setItemCountPerPage(20);
        }
        else {
            $list  = $table->find($search, "{$sort->field} {$sort->direction}");
        }

        // The list of years we give the block should all years available
        if (isset($search['year'])) { unset($search['year']); }

        $this->template->blocks[] = new Block('meetingFiles/list.inc', [
            'files'     => $list,
            'committee' => isset($committee) ? $committee : null,
            'sort'      => $sort,
            'years'     => array_keys($table->years($search))
        ]);
        return $this->template;
    }

    public function years(): View
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
        return $this->template;
    }

    public function update(): View
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
                    $file->setType        ($_POST['type'        ]);
                    $file->setTitle       ($_POST['title'       ]);
                    $file->setEventId     ($_POST['eventId'     ]);
                    $file->setCommittee_id($_POST['committee_id']);
                    if (!empty($_POST['meetingDate'])) {
                        $file->setMeetingDate ($_POST['meetingDate' ], 'Y-m-d');
                    }
                    else {
                        $file->setMeetingDate(null);
                    }
                    // Before we save the file, make sure all the database information is correct
                    $file->validateDatabaseInformation();
                    // If they are editing an existing document, they do not need to upload a new file
                    if (isset($_FILES['meetingFile']) && $_FILES['meetingFile']['error'] != UPLOAD_ERR_NO_FILE) {
                        $file->setFile($_FILES['meetingFile']);
                    }

                    $file->save();

                    $return_url = !empty($_POST['return_url'])
                                  ? $_POST['return_url']
                                  : View::generateUrl('meetingFiles.index')."?committee_id={$file->getCommittee_id()}";
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
        return $this->template;
    }

    public function download(): View
    {
        if (!empty($_GET['meetingFile_id'])) {
            try {
                $file = new MeetingFile($_GET['meetingFile_id']);
                $file->sendToBrowser();
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
        return $this->template;
    }

    public function delete(): View
    {
        if (!empty($_GET['meetingFile_id'])) {
            try {
                $file       = new MeetingFile($_GET['meetingFile_id']);
                $committee  = $file->getCommittee();
                $return_url = View::generateUrl('meetingFiles.index').'?committee_id='.$committee->getId();
                $file->delete();
                header("Location: $return_url");
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
        return $this->template;
    }
}
