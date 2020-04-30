<?php
/**
 * @copyright 2014-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Controllers;

use Application\Models\ApplicantFile;
use Application\Models\ApplicantFilesTable;

use Web\Controller;
use Web\Block;
use Web\View;

class ApplicantFilesController extends Controller
{
	public function download(): View
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
        return $this->template;
	}

	public function delete(): View
	{
        if (!empty($_GET['applicantFile_id'])) {
            try {
                $file       = new ApplicantFile($_GET['applicantFile_id']);
                $return_url = View::generateUrl('applicants.view').'?applicant_id='.$file->getApplicant_id();

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
