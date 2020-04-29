<?php
/**
 * @copyright 2016-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Controllers;

use Application\Models\Applicant;
use Application\Models\ApplicantTable;
use Application\Models\Captcha;
use Application\Models\ApplicantFile;
use Application\Models\Committee;

use Web\Controller;
use Web\Block;
use Web\Database;
use Web\View;

class ApplicantsController extends Controller
{
    public function index(): View
    {
        $table = new ApplicantTable();
        $list = $table->find();

        $title = $this->template->_(['applicant', 'applicants', count($list)]);
        $this->template->title = $title.' - '.APPLICATION_HOME;
        $this->template->blocks[] = new Block('applicants/list.inc', ['applicants'=>$list]);
        return $this->template;
    }

    public function view(): View
    {
        if (!empty($_REQUEST['applicant_id'])) {
            try { $applicant = new Applicant($_REQUEST['applicant_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }

        if (isset($applicant)) {
            $this->template->blocks[] = new Block('applicants/info.inc', ['applicant'=>$applicant]);
            $this->template->blocks[] = new Block('applications/list.inc', [
                'applicant'    => $applicant,
                'applications' => $applicant->getApplications(['current'=>time()]),
                'title'        => $this->template->_('applications_current'),
                'type'         => 'current'
            ]);
            $this->template->blocks[] = new Block('applications/list.inc', [
                'applicant'    => $applicant,
                'applications' => $applicant->getApplications(['archived'=>time()]),
                'title'        => $this->template->_('applications_archived'),
                'type'         => 'archived'
            ]);
        }
        else {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
        }
        return $this->template;
    }

    public function update(): View
    {
        if (!empty($_REQUEST['applicant_id'])) {
            try { $applicant = new Applicant($_REQUEST['applicant_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }

        if (isset($applicant)) {
            if (isset($_POST['applicant_id'])) {
                try {
                    $applicant->handleUpdate($_POST);
                    $applicant->save();

                    if (isset(   $_FILES['applicantFile'])) {
                        foreach ($_FILES['applicantFile']['name'] as $id=>$name) {
                            if ( $_FILES['applicantFile']['error'][$id] === UPLOAD_ERR_OK) {
                                $fileinfo = [
                                    'name'     => $_FILES['applicantFile']['name'    ][$id],
                                    'type'     => $_FILES['applicantFile']['type'    ][$id],
                                    'tmp_name' => $_FILES['applicantFile']['tmp_name'][$id],
                                    'error'    => $_FILES['applicantFile']['error'   ][$id],
                                    'size'     => $_FILES['applicantFile']['size'    ][$id]
                                ];
                                $file = new ApplicantFile($id);
                                $file->setFile($fileinfo);
                                $file->save();
                            }
                        }
                    }
                    header('Location: '.BASE_URI.'/applicants/view?applicant_id='.$applicant->getId());
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
            }

            $this->template->blocks[] = new Block('applicants/updateForm.inc', ['applicant'=>$applicant]);
        }
        else {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
        }
        return $this->template;
    }

    public function apply(): View
    {
        $applicant = new Applicant();

        if (isset($_POST['firstname']) && Captcha::verify()) {
            $db = Database::getConnection();
            $db->getDriver()->getConnection()->beginTransaction();
            try {

                $applicant->handleUpdate($_POST);
                $applicant->save();
                if (isset($_POST['committees'])) {
                    $applicant->saveCommittees($_POST['committees']);
                }

                if (isset($_FILES['applicantFile'])
                    &&    $_FILES['applicantFile']['error'] === UPLOAD_ERR_OK) {
                    $file = new ApplicantFile();
                    $file->setApplicant_id($applicant->getId());
                    $file->setFile($_FILES['applicantFile']);
                    $file->save();
                }
                $db->getDriver()->getConnection()->commit();

                $this->notifyLiaisons($applicant);
                $this->template->blocks[] = new Block('applicants/success.inc', ['applicant'=>$applicant]);

                return $this->template;
            }
            catch (\Exception $e) {
                $db->getDriver()->getConnection()->rollback();
                $_SESSION['errorMessages'][] = $e;
            }
        }


        $title = $this->template->_('apply');
        if (isset($_REQUEST['committee_id'])) {
            try {
                $committee = new Committee($_REQUEST['committee_id']);
                $title.= ' - '.$committee->getName();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }
        $this->template->title = $title.' - '.APPLICATION_NAME;
        $block = new Block('applicants/applyForm.inc', ['applicant'=>$applicant]);
        if (isset($committee)) { $block->committee = $committee; }
        $this->template->blocks[] = $block;
        return $this->template;
    }

    public function delete(): View
    {
        if (!empty($_REQUEST['applicant_id'])) {
            try { $applicant = new Applicant($_REQUEST['applicant_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }

        if (isset($applicant)) {
            $applicant->delete();
            header('Location: '.BASE_URL.'/applicants');
            exit();
        }
        else {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
        }
        return $this->template;
    }

    private function notifyLiaisons(Applicant $applicant)
    {
        foreach ($applicant->getApplications() as $a) {
            $people = $a->getPeopleToNotify();
            if (count($people)) {
                $b = new Block('liaisons/applicationNotification.inc', ['application'=>$a]);
                $message = $b->render('txt', $this->template);
                $subject = sprintf($this->template->_('board_application_subject', 'messages'), $a->getCommittee()->getName());
                foreach ($people as $p) {
                    $p->sendNotification($message, $subject);
                }
            }
        }
    }
}
