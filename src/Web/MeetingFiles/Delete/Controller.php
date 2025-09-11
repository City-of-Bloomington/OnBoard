<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\MeetingFiles\Delete;

use Application\Models\MeetingFile;
use Application\Models\MeetingFilesTable;
use Application\Models\CommitteeTable;
use Web\Search\Solr;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_REQUEST['meetingFile_id'])) {
            try {
                $file       = new MeetingFile($_REQUEST['meetingFile_id']);
                $committee  = $file->getCommittee();
                $return_url = \Web\View::generateUrl('meetings.view', ['meeting_id'=>$file->getMeeting_id()]);

                global $SOLR;
                if ($SOLR) {
                    $solr = new Solr($SOLR['onboard']);
                    $solr->delete($file);
                }

                $file->delete();
                header("Location: $return_url");
                exit();
            }
            catch (\Exception $e) {
                $_SESSION['errorMessages'][] = $e->getMessage();
            }
        }

        return new \Web\Views\NotFoundView();
    }
}
