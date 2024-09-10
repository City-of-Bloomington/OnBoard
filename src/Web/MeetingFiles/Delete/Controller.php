<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namsepace Web\MeetingFiles\Delete;

use Application\Models\meetingFile;

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
}
