<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Committees\Meetings\Sync;

use Application\Models\Committee;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_REQUEST['committee_id'])) {
            try { $committee = new Committee($_REQUEST['committee_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        if (!isset($committee)) {
            return \Web\Views\NotFoundView();
        }

        $committee->syncGoogleCalendar();

        header('Location: '.\Web\View::generateUrl('committees.meetings', ['committee_id'=>$committee->getId()]));
        exit();
    }
}
