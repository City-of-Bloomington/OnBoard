<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Meetings\Update;

use Application\Models\Meeting;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_REQUEST['meeting_id'])) {
            try { $meeting = new Meeting($_REQUEST['meeting_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        if (!isset($meeting)) { return new \Web\Views\NotFoundView(); }

        $return_url = \Web\View::generateUrl('meetings.view', ['meeting_id'=>$meeting->getId()]);

        if (isset($_POST['title'])) {
            $meeting->setTitle   ($_POST['title'   ]);
            $meeting->setLocation($_POST['location']);
            $meeting->setEventId ($_POST['eventId' ]);
            $meeting->setHtmlLink($_POST['htmlLink']);

            try {
                $meeting->save();
                header("Location: $return_url");
                exit();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        return new View($meeting, $return_url);
    }
}
