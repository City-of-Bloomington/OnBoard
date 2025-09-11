<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\MeetingFiles\Add;

use Application\Models\Meeting;
use Application\Models\MeetingFile;

use Web\MeetingFiles\Update\Controller as UpdateController;
use Web\MeetingFiles\Update\View       as UpdateView;


class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        $file = new MeetingFile();

        if (!$file->getMeeting_id()) {
            if (!empty($_REQUEST['meeting_id'])) {
                try {
                    $m = new Meeting($_REQUEST['meeting_id']);
                    $file->setMeeting($m);
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
            }
        }

        if (!empty($_REQUEST['type'])) { $file->setType($_REQUEST['type']); }

        if (!$file->getMeeting_id()) {
            return new \Web\Views\NotFoundView();
        }

        if (isset($_POST['type'])) {
            try { UpdateController::saveAndRedirect($file); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        return new UpdateView($file);
    }
}
