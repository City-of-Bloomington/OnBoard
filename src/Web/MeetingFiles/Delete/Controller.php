<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
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
        if (!empty($_GET['meetingFile_id'])) {
            try {
                $file       = new MeetingFile($_GET['meetingFile_id']);
                $committee  = $file->getCommittee();
                $return_url = \Web\View::generateUrl('meetingFiles.index').'?committee_id='.$committee->getId();

                global $SOLR;
                $solr = new Solr($SOLR['onboard']);
                $solr->delete($file);

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

    /**
     * ACL will call this function when a role needs to check the Department Association
     *
     * @see Web\Auth\DepartmentAssociation
     */
    public static function hasDepartment(int $department_id): bool
    {
        if (!empty($_GET['committee_id'])) {
            return CommitteeTable::hasDepartment($department_id, (int)$_GET['committee_id']);
        }
        if (!empty($_REQUEST['meetingFile_id'])) {
            return MeetingFilesTable::hasDepartment($department_id, (int)$_REQUEST['meetingFile_id']);
        }
        return false;
    }
}
