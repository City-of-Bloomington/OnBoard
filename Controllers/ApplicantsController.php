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
        $this->template->blocks[] = new Block('applicants/updateForm.inc', ['applicant'=>$applicant]);
    }
}