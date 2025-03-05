<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Applicants\Apply;

use Application\Models\Applicant;
use Application\Models\ApplicantFile;
use Application\Models\Captcha;
use Application\Models\Committee;
use Web\Database;

use ReCaptcha\ReCaptcha;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        $applicant = new Applicant();

        if (isset($_POST['firstname']) && !empty($_POST['g-recaptcha-response'])) {
            $rc  = new ReCaptcha(RECAPTCHA_SERVER_KEY);
            $r   = $rc->setExpectedHostname(BASE_HOST)->verify($_POST['g-recaptcha-response']);
            if ($r->isSuccess()) {
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
                        return new Success($applicant);
                }
                catch (\Exception $e) {
                    $db->getDriver()->getConnection()->rollback();
                    $_SESSION['errorMessages'][] = $e->getMessage();
                }
            }
        }


        if (isset($_REQUEST['committee_id'])) {
            try {
                $committee = new Committee($_REQUEST['committee_id']);
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        return new View($applicant, $committee ?? null);
    }

    private function notifyLiaisons(Applicant $applicant)
    {
        foreach ($applicant->getApplications() as $a) {
            $people = $a->getPeopleToNotify();
            if (count($people)) {
                $email   = new Email($a);
                $email->outputFormat = 'txt';
                $message = $email->render();
                $subject = sprintf($email->_('board_application_subject', 'messages'), $a->getCommittee()->getName());
                foreach ($people as $p) {
                    $p->sendNotification($message, $subject);
                }
            }
        }
    }
}
