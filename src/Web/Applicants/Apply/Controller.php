<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Applicants\Apply;

use Application\Models\Application;
use Application\Models\Committee;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_REQUEST['committee_id'])) {
            try {
                $committee = new Committee($_REQUEST['committee_id']);
                if (!$committee->takesApplications()) {
                    unset($committee);
                }
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        if (!isset($committee)) { return new \Web\Views\NotFoundView(); }

        $application = new Application();
        $application->setCommittee($committee);
        $application->setPerson($_SESSION['USER']);

        if (isset($_POST['interest'])) {
            try {
                $application->handleUpdate($_POST);
                self::notifyLiaisons($application);
                $application->save();
                return new Success($application);
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        return new View($_POST ?? [], $committee);
    }

    private static function notifyLiaisons(Application $a)
    {
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
