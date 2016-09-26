<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Controllers;

use Application\Models\Applicant;
use Application\Models\ApplicantTable;
use Application\Models\Captcha;
use Application\Models\Media;
use Application\Models\Committee;
use Blossom\Classes\Controller;
use Blossom\Classes\Block;
use Blossom\Classes\Database;

class ApplicantsController extends Controller
{
    public function index()
    {
        $table = new ApplicantTable();
        $list = $table->find();

        $title = $this->template->_(['applicant', 'applicants', count($list)]);
        $this->template->title = $title.' - '.APPLICATION_HOME;
        $this->template->blocks[] = new Block('applicants/list.inc', ['applicants'=>$list]);
    }

    public function view()
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
    }

    public function update()
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

                    if (isset(   $_FILES['mediafile'])) {
                        foreach ($_FILES['mediafile']['name'] as $id=>$name) {
                            if ( $_FILES['mediafile']['error'][$id] === UPLOAD_ERR_OK) {
                                $file = [
                                    'name'     => $_FILES['mediafile']['name'    ][$id],
                                    'type'     => $_FILES['mediafile']['type'    ][$id],
                                    'tmp_name' => $_FILES['mediafile']['tmp_name'][$id],
                                    'error'    => $_FILES['mediafile']['error'   ][$id],
                                    'size'     => $_FILES['mediafile']['size'    ][$id]
                                ];
                                $media = new Media($id);
                                $media->setFile($file);
                                $media->save();
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
    }

    public function apply()
    {
        $applicant = new Applicant();

        if (isset($_POST['firstname']) && Captcha::verify()) {
            $zend_db = Database::getConnection();
            $zend_db->getDriver()->getConnection()->beginTransaction();
            try {

                $applicant->handleUpdate($_POST);
                $applicant->save();
                if (isset($_POST['committees'])) {
                    $applicant->saveCommittees($_POST['committees']);
                }

                if (isset($_FILES['mediafile'])
                    &&    $_FILES['mediafile']['error'] === UPLOAD_ERR_OK) {
                    $media = new Media();
                    $media->setApplicant_id($applicant->getId());
                    $media->setFile($_FILES['mediafile']);
                    $media->save();
                }
                $zend_db->getDriver()->getConnection()->commit();

                $this->template->blocks[] = new Block('applicants/success.inc', ['applicant'=>$applicant]);

                return;
            }
            catch (\Exception $e) {
                $zend_db->getDriver()->getConnection()->rollback();
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
    }

    public function delete()
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
    }
}