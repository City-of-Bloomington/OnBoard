<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Applications\Archive;

use Application\Models\Application;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_REQUEST['application_id'])) {
            try { $application = new Application($_REQUEST['application_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        if (isset($application)) {
            try { $application->archive(); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }

            $url = \Web\View::generateUrl('committees.applications', ['id'=>$application->getCommittee_id()]);
            header("Location: $url");
            exit();
        }

        return \Web\Views\NotFoundView();
    }
}
