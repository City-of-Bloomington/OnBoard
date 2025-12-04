<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Meetings\Delete;

use Application\Models\Meeting;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_REQUEST['meeting_id'])) {
            try { $meeting = new Meeting($_REQUEST['meeting_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        if (isset($meeting)) {
            $return_url = \Web\View::generateUrl('committees.meetings', ['committee_id'=>$meeting->getCommittee_id()]);

            try { $meeting->delete(); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }

            header("Location: $return_url");
            exit();
        }

        return new \Web\Views\NotFoundView();
    }
}
