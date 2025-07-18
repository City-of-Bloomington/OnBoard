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

use Google\Cloud\RecaptchaEnterprise\V1\Client\RecaptchaEnterpriseServiceClient;
use Google\Cloud\RecaptchaEnterprise\V1\Event;
use Google\Cloud\RecaptchaEnterprise\V1\Assessment;
use Google\Cloud\RecaptchaEnterprise\V1\CreateAssessmentRequest;
use Google\Cloud\RecaptchaEnterprise\V1\TokenProperties\InvalidReason;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        $applicant = new Applicant();

        if (isset($_POST['firstname']) && !empty($_POST['g-recaptcha-response'])) {
            if (self::recaptcha_is_valid($_POST['g-recaptcha-response'])) {
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
                $message = $email->render();
                $subject = sprintf($email->_('board_application_subject', 'messages'), $a->getCommittee()->getName());
                foreach ($people as $p) {
                    $p->sendNotification($message, $subject);
                }
            }
        }
    }

    /**
     * @see https://cloud.google.com/recaptcha/docs/create-assessment-website
     */
    private static function recaptcha_is_valid(string $token): bool
    {
        // TODO: To avoid memory issues, move this client generation outside
        // of this example, and cache it (recommended) or call client.close()
        // before exiting this method.
        $credentials =
        $client      = new RecaptchaEnterpriseServiceClient(['credentials'=>GOOGLE_CREDENTIALS_FILE]);
        $projectName = $client->projectName(RECAPTCHA_PROJECT_ID);
        $event       = (new Event())->setSiteKey(RECAPTCHA_SITE_KEY)->setToken($token);
        $assessment  = (new Assessment())->setEvent($event);
        $request     = (new CreateAssessmentRequest())->setParent($projectName)->setAssessment($assessment);

        try {
            $response = $client->createAssessment($request);
            $is_valid = $response->getTokenProperties()->getValid();
        }
        catch (exception $e) {
            $is_valid = false;
        }
        $client->close();
        return $is_valid;
    }
}
