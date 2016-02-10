<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Controllers;

use Application\Models\Applicant;
use Application\Models\ApplicantTable;
use Blossom\Classes\Controller;
use Blossom\Classes\Block;

class ApplicantsController extends Controller
{
    public function index()
    {
        $table = new ApplicantTable();
        $list = $table->find();

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
                'applications' => $applicant->getApplications()
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

        if (isset($_POST['firstname'])) {
            try {
                $applicant->handleUpdate($_POST);
                $applicant->save();
                $applicant->saveCommittees($_POST['committees']);

                $this->template->blocks[] = new Block('applicants/success.inc', ['applicant'=>$applicant]);

                return;
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }
        $this->template->blocks[] = new Block('applicants/applyForm.inc', ['applicant'=>$applicant]);
    }

}