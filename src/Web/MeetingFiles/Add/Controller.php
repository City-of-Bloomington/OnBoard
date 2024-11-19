<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\MeetingFiles\Add;

use Application\Models\Meeting;
use Application\Models\MeetingFile;

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

        $meeting   = $file->getMeeting();
        $committee = $meeting->getCommittee();
        if (isset($_POST['type'])) {
            try {
                $file->setType      ($_POST['type'      ]);
                $file->setTitle     ($_POST['title'     ]);
                $file->setMeeting_id($_POST['meeting_id']);
                $file->setUpdatedPerson($_SESSION['USER']);

                // Before we save the file, make sure all the database information is correct
                $file->validateDatabaseInformation();
                // If they are editing an existing document, they do not need to upload a new file
                if (isset($_FILES['meetingFile']) && $_FILES['meetingFile']['error'] != UPLOAD_ERR_NO_FILE) {
                    $file->setFile($_FILES['meetingFile']);
                }

                $file->save();

                $url = !empty($_POST['return_url'])
                            ? $_POST['return_url']
                            : \Web\View::generateUrl('meetingFiles.index')."?committee_id={$committee->getId()}";
                header("Location: $url");
                exit();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        return new \Web\MeetingFiles\Update\View($file, $committee);

    }
}
