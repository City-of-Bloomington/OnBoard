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
use Application\Models\Notifications\DefinitionTable;

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

        if (isset($_POST['type'])) {
            try { self::saveAndRedirect($file); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        return new View($file);
    }

    /**
     * @throws \Exception
     */
    public static function saveAndRedirect(MeetingFile $file)
    {
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
        self::notify($file);

        $return_url = \Web\View::generateUrl('meetings.view', ['meeting_id'=>$file->getMeeting_id()]);
        header("Location: $return_url");
        exit();
    }

    public static function notify(MeetingFile $f)
    {
        $t = new DefinitionTable();

        $n = $t->loadForSending(__NAMESPACE__.'::notice', $f->getMeeting()->getCommittee_id());
        if (isset($n)) { $n->send([], $f); }
    }
}
