<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Committees\Applications;

use Application\Models\Committee;
use \Web\View;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): View
    {
        if (!empty($_GET['committee_id'])) {
            try {
                $committee             = new Committee($_GET['committee_id']);
                $seats                 = [];
                $applications_current  = [];
                $applications_archived = [];

                foreach ($committee->getApplications(['current' =>time()]) as $a) {
                    $applications_current[] = $a;
                }
                foreach ($committee->getApplications(['archived'=>time()]) as $a) {
                    $applications_archived[] = $a;
                }
                foreach ($committee->getSeats(['current'=>true]) as $a) {
                    $seats[] = $a;
                }

                return View::isAllowed('applications', 'report')
                       ? new ReportView($committee, $applications_current, $applications_archived, $seats)
                       : new   ListView($committee, $applications_current, $applications_archived);

            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        return new \Web\Views\NotFoundView();
    }
}
