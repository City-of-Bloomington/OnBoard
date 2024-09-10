<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\MeetingFiles\Update;

use Application\Models\MeetingFile;
use Application\Models\Committee;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_REQUEST['meetingFile_id'])) {
            try { $file = new MeetingFile($_REQUEST['meetingFile_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }
        else { $file = new MeetingFile(); }

        if (!$file->getCommittee_id()) {
            if (!empty($_REQUEST['committee_id'])) {
                try {
                    $c = new Committee($_REQUEST['committee_id']);
                    $file->setCommittee($c);
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
            }
        }

        if (!empty($_REQUEST['type'])) { $file->setType($_REQUEST['type']); }

        if (isset($file) && $file->getCommittee_id()) {
            if (isset($_POST['type'])) {
                try {
                    $file->setType        ($_POST['type'        ]);
                    $file->setTitle       ($_POST['title'       ]);
                    $file->setCommittee_id($_POST['committee_id']);
                    $file->setUpdatedPerson($_SESSION['USER']);
                    if (!empty($_POST['eventId'])) {
                        $file->setEventId($_POST['eventId']);
                    }
                    if (!empty($_POST['meetingDate'])) {
                        $file->setMeetingDate($_POST['meetingDate' ], 'Y-m-d');
                    }
                    else {
                        $file->setMeetingDate(null);
                    }
                    // Before we save the file, make sure all the database information is correct
                    $file->validateDatabaseInformation();
                    // If they are editing an existing document, they do not need to upload a new file
                    if (isset($_FILES['meetingFile']) && $_FILES['meetingFile']['error'] != UPLOAD_ERR_NO_FILE) {
                        $file->setFile($_FILES['meetingFile']);
                    }

                    $file->save();

                    $return_url = !empty($_POST['return_url'])
                                ? $_POST['return_url']
                                : \Web\View::generateUrl('meetingFiles.index')."?committee_id={$file->getCommittee_id()}";
                    header("Location: $return_url");
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
            }

            $committee = $file->getCommittee();

            return new View($file, $committee);
        }

        return new \Web\Views\NotFoundView();
    }
}
