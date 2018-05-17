<?php
/**
 * @copyright 2014-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Controllers;

use Application\Models\ApplicantFile;
use Application\Models\ApplicantFilesTable;
use Blossom\Classes\Controller;
use Blossom\Classes\Block;

class ApplicantFilesController extends Controller
{
	public function index()
	{
	}

	public function download()
	{
        if (!empty($_GET['applicantFile_id'])) {
            try {
                $file = new ApplicantFile($_GET['applicantFile_id']);
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
	}

	public function delete()
	{
        if (!empty($_GET['applicantFile_id'])) {
            try {
                $file = new ApplicantFile($_GET['applicantFile_id']);
                $return_url = BASE_URL.'/applicants/view?applicant_id='.$file->getApplicant_id();

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
	}
}
