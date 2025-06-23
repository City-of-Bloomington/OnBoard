<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\MeetingFiles\Update;

use Application\Models\Meeting;
use Application\Models\MeetingFile;
use Application\Models\MeetingFilesTable;
use Application\Models\Committee;
use Application\Models\CommitteeTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_REQUEST['meetingFile_id'])) {
            try { $file = new MeetingFile($_REQUEST['meetingFile_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }
        else { $file = new MeetingFile(); }

        if (!isset($file)) {
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

                $return_url = \Web\View::generateUrl('meetings.view', ['meeting_id'=>$file->getMeeting_id()]);
                header("Location: $return_url");
                exit();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        return new View($file, $committee);
    }

    /**
     * ACL will call this function before invoking the Controller
     *
     * When a role needs to check the Department Association, the ACL will
     * be checked before invoking the Controller.  This function must be called
     * statically.  The current route parameters will be passed.  These parameters
     * will be the same as would be passed to __invoke().
     *
     * @see Web\Auth\DepartmentAssociation
     * @see access_control.php
     */
    public static function hasDepartment(int $department_id, array $params): bool
    {
        if (!empty($_REQUEST['meetingFile_id'])) {
            return MeetingFilesTable::hasDepartment($department_id, (int)$_REQUEST['meetingFile_id']);
        }

        return false;
    }
}
